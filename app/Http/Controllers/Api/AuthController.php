<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ResidentDocument;
use App\Models\Resident;
use App\Models\User;
use App\Notifications\AccountPendingNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;

/**
 * API Auth — login / register / logout (Req 9 AC1)
 * Returns structured JSON, proper HTTP codes, and 422 with errors key on validation failure.
 */
class AuthController extends Controller
{
    /**
     * POST /api/setup/admin
     * One-time endpoint — creates the first admin account.
     * Blocked once any admin account already exists.
     */
    public function setupAdmin(Request $request): JsonResponse
    {
        // Safety guard — only works on a fresh database with no admin yet
        if (User::where('role', 'admin')->exists()) {
            return response()->json([
                'message' => 'An admin account already exists. This endpoint is disabled.',
            ], 403);
        }

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', Rules\Password::min(8)],
        ]);

        $admin = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'admin',
            'status'   => 'active',
        ]);

        $token = $admin->createToken('admin-setup-token')->plainTextToken;

        return response()->json([
            'message' => 'Admin account created successfully.',
            'token'   => $token,
            'user'    => [
                'id'     => $admin->id,
                'name'   => $admin->name,
                'email'  => $admin->email,
                'role'   => $admin->role,
                'status' => $admin->status,
            ],
        ], 201);
    }

    /**
     * POST /api/auth/login
     * Returns 401 on bad credentials.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'The provided credentials are incorrect.',
                'errors'  => ['email' => ['The provided credentials are incorrect.']],
            ], 401);
        }

        // Reject pending/rejected resident accounts from API login as well
        if ($user->isResident() && ! $user->isActive()) {
            return response()->json([
                'message' => 'Account is not active.',
                'status'  => $user->status,
                'errors'  => ['email' => ['Your account status is: ' . str_replace('_', ' ', $user->status)]],
            ], 403);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful.',
            'token'   => $token,
            'user'    => [
                'id'     => $user->id,
                'name'   => $user->name,
                'email'  => $user->email,
                'role'   => $user->role,
                'status' => $user->status,
            ],
        ], 200);
    }

    /**
     * POST /api/auth/register
     * Returns 201. Account starts as pending_verification.
     */
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|max:255|unique:users,email',
            'password'      => ['required', 'confirmed', Rules\Password::min(8)],
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'birth_date'    => ['required', 'date', 'before:today', function ($a, $v, $fail) {
                if (\Carbon\Carbon::parse($v)->age < 15) {
                    $fail('You must be at least 15 years old to register.');
                }
            }],
            'gender'        => 'required|in:male,female',
            'contact_number'=> 'required|string|min:7|max:15',
            'address'       => 'required|string|max:500',
        ]);

        $user = DB::transaction(function () use ($request) {
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role'     => 'resident',
                'status'   => 'pending_verification',
            ]);

            Resident::create([
                'user_id'        => $user->id,
                'email'          => $request->email,
                'first_name'     => $request->first_name,
                'middle_name'    => $request->middle_name ?? null,
                'last_name'      => $request->last_name,
                'birth_date'     => $request->birth_date,
                'gender'         => $request->gender,
                'contact_number' => $request->contact_number,
                'address'        => $request->address,
                'source'         => 'self_registration',
            ]);

            $user->notify(new AccountPendingNotification());

            return $user;
        });

        return response()->json([
            'message' => 'Registration submitted. Account is pending verification by a barangay admin.',
            'user'    => [
                'id'     => $user->id,
                'name'   => $user->name,
                'email'  => $user->email,
                'role'   => $user->role,
                'status' => $user->status,
            ],
        ], 201);
    }

    /**
     * POST /api/auth/logout
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully.'], 200);
    }
}
