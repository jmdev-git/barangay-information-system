<?php

namespace App\Http\Controllers;

use App\Models\Clearance;
use App\Models\Blotter;
use App\Models\Household;
use App\Models\Resident;
use App\Models\ResidentDocument;
use App\Models\User;
use App\Services\CloudinaryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Unified Resident & Household Management (Req 4).
 * Replaces the old separate Residents and Households sections.
 */
class ResidentController extends Controller
{
    // -------------------------------------------------------------------------
    // Index — searchable list (Req 4 AC6)
    // -------------------------------------------------------------------------
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $status = $request->query('status');
        $gender = $request->query('gender');

        $residents = Resident::with(['user', 'household', 'documents'])
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('first_name',     'like', "%{$search}%")
                      ->orWhere('last_name',     'like', "%{$search}%")
                      ->orWhere('contact_number','like', "%{$search}%")
                      ->orWhere('email',         'like', "%{$search}%")
                      ->orWhereHas('household', fn ($hq) =>
                            $hq->where('address', 'like', "%{$search}%")
                               ->orWhere('purok',  'like', "%{$search}%"))
                      ->orWhereHas('user', fn ($uq) =>
                            $uq->where('name',  'like', "%{$search}%")
                               ->orWhere('email','like', "%{$search}%"));
                });
            })
            ->when($gender, fn ($q) => $q->where('gender', $gender))
            ->when($status, fn ($q) => $q->whereHas('user', fn ($uq) => $uq->where('status', $status)))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.residents.index', compact('residents', 'search', 'gender', 'status'));
    }

    // -------------------------------------------------------------------------
    // Create — with inline household option (Req 4 AC2)
    // -------------------------------------------------------------------------
    public function create(): View
    {
        $households = Household::orderBy('address')->get();
        return view('admin.residents.create', compact('households'));
    }

    // -------------------------------------------------------------------------
    // Store (Req 4 AC2, AC5)
    // -------------------------------------------------------------------------
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            // Account
            'user_name'      => 'required|string|max:255',
            'email'          => 'required|email|max:255|unique:users,email',
            'password'       => 'required|string|min:8',
            // Household — either pick existing or create new inline
            'household_mode' => 'required|in:existing,new',
            'household_id'   => 'required_if:household_mode,existing|nullable|exists:households,id',
            'new_address'    => 'required_if:household_mode,new|nullable|string|max:500',
            'new_purok'      => 'nullable|string|max:100',
            'new_barangay'   => 'required_if:household_mode,new|nullable|string|max:100',
            // Resident profile
            'first_name'           => 'required|string|max:255',
            'middle_name'          => 'nullable|string|max:255',
            'last_name'            => 'required|string|max:255',
            'birth_date'           => 'required|date|before:today',
            'gender'               => 'required|in:male,female',
            'civil_status'         => 'required|in:single,married,widowed,separated,annulled',
            'contact_number'       => 'required|string|min:7|max:15',
            'relationship_to_head' => 'nullable|string|max:100',
        ]);

        DB::transaction(function () use ($validated, $request) {
            // Resolve household
            if ($validated['household_mode'] === 'new') {
                $household = Household::create([
                    'address'  => $validated['new_address'],
                    'purok'    => $validated['new_purok']    ?? null,
                    'barangay' => $validated['new_barangay'] ?? 'N/A',
                ]);
            } else {
                $household = Household::findOrFail($validated['household_id']);
            }

            // Create user account (active — admin-created accounts bypass pending)
            $user = User::create([
                'name'     => $validated['user_name'],
                'email'    => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role'     => 'resident',
                'status'   => 'active',
            ]);

            // Create resident profile (Req 4 AC5 — address from household)
            $resident = Resident::create([
                'user_id'              => $user->id,
                'email'                => $validated['email'],
                'household_id'         => $household->id,
                'first_name'           => $validated['first_name'],
                'middle_name'          => $validated['middle_name'] ?? null,
                'last_name'            => $validated['last_name'],
                'birth_date'           => $validated['birth_date'],
                'gender'               => $validated['gender'],
                'civil_status'         => $validated['civil_status'],
                'contact_number'       => $validated['contact_number'],
                'relationship_to_head' => $validated['relationship_to_head'] ?? null,
                'address'              => $household->address,
                'source'               => 'census',
                'verified_by'          => auth()->id(),
            ]);
        });

        return redirect()->route('residents.index')
            ->with('success', 'Resident profile and account created successfully.');
    }

    // -------------------------------------------------------------------------
    // Show — single page with all resident + household data (Req 4 AC3)
    // -------------------------------------------------------------------------
    public function show(Resident $resident): View
    {
        $resident->load([
            'user',
            'household.residents',
            'documents',
            'clearances',
            'complainantBlotters',
            'respondentBlotters',
            'verifier',
        ]);

        return view('admin.residents.show', compact('resident'));
    }

    // -------------------------------------------------------------------------
    // Edit
    // -------------------------------------------------------------------------
    public function edit(Resident $resident): View
    {
        $households = Household::orderBy('address')->get();
        return view('admin.residents.edit', compact('resident', 'households'));
    }

    // -------------------------------------------------------------------------
    // Update (Req 4 AC5 — sync address when household changes)
    // -------------------------------------------------------------------------
    public function update(Request $request, Resident $resident): RedirectResponse
    {
        $validated = $request->validate([
            'user_name'            => 'required|string|max:255',
            'email'                => ['required','email','max:255',
                                       Rule::unique('users','email')->ignore($resident->user_id),
                                       Rule::unique('residents','email')->ignore($resident->id)],
            'password'             => 'nullable|string|min:8',
            'household_id'         => 'nullable|exists:households,id',
            'first_name'           => 'required|string|max:255',
            'middle_name'          => 'nullable|string|max:255',
            'last_name'            => 'required|string|max:255',
            'birth_date'           => 'required|date|before:today',
            'gender'               => 'required|in:male,female',
            'civil_status'         => 'required|in:single,married,widowed,separated,annulled',
            'contact_number'       => 'required|string|min:7|max:15',
            'relationship_to_head' => 'nullable|string|max:100',
            'status'               => 'nullable|in:active,pending_verification,rejected',
        ]);

        DB::transaction(function () use ($validated, $resident) {
            // Sync household address (Req 4 AC5)
            $household = $validated['household_id']
                ? Household::find($validated['household_id'])
                : null;

            // Update user account
            if ($resident->user) {
                $userData = [
                    'name'  => $validated['user_name'],
                    'email' => $validated['email'],
                ];
                if (!empty($validated['password'])) {
                    $userData['password'] = Hash::make($validated['password']);
                }
                if (!empty($validated['status'])) {
                    $userData['status'] = $validated['status'];
                }
                $resident->user->update($userData);
            }

            // Update resident profile
            $resident->update([
                'email'                => $validated['email'],
                'household_id'         => $validated['household_id'] ?? null,
                'first_name'           => $validated['first_name'],
                'middle_name'          => $validated['middle_name'] ?? null,
                'last_name'            => $validated['last_name'],
                'birth_date'           => $validated['birth_date'],
                'gender'               => $validated['gender'],
                'civil_status'         => $validated['civil_status'],
                'contact_number'       => $validated['contact_number'],
                'relationship_to_head' => $validated['relationship_to_head'] ?? null,
                // Sync address from household (Req 4 AC5)
                'address'              => $household?->address ?? $resident->address,
            ]);
        });

        return redirect()->route('residents.show', $resident)
            ->with('success', 'Resident profile updated successfully.');
    }

    // -------------------------------------------------------------------------
    // Destroy — cascade delete user + blotters + clearances (Req 4 AC7)
    // -------------------------------------------------------------------------
    public function destroy(Resident $resident): RedirectResponse
    {
        DB::transaction(function () use ($resident) {
            // Delete uploaded ID documents from Cloudinary (and legacy local storage)
            $cloudinary = app(CloudinaryService::class);
            foreach ($resident->documents as $doc) {
                if ($doc->cloudinary_url && $doc->file_path) {
                    $cloudinary->delete($doc->file_path, $doc->mime_type ?? 'image/jpeg');
                } else {
                    Storage::disk('private')->delete($doc->file_path);
                }
            }
            $resident->documents()->delete();

            // Delete clearances (Req 4 AC7)
            $resident->clearances()->delete();

            // Delete blotters where resident is complainant OR respondent (Req 4 AC7)
            Blotter::where('complainant_id', $resident->id)
                   ->orWhere('respondent_id',  $resident->id)
                   ->delete();

            // Delete user account
            $resident->user?->delete();

            // Delete resident profile
            $resident->delete();
        });

        return redirect()->route('residents.index')
            ->with('success', 'Resident, associated account, clearances, and blotter records deleted.');
    }
}
