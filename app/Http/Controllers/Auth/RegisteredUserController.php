<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Resident;
use App\Models\ResidentDocument;
use App\Models\User;
use App\Notifications\AccountPendingNotification;
use App\Services\CloudinaryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Show the registration form (Req 1).
     */
    public function create(): View
    {
        return view('auth.register', [
            'documentTypes' => ResidentDocument::DOCUMENT_TYPES,
        ]);
    }

    /**
     * Handle registration submission (Req 1 AC1–AC9).
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            // Basic account fields
            'name'             => ['required', 'string', 'max:255'],
            'email'            => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password'         => ['required', 'confirmed', Rules\Password::min(8)],

            // Resident profile fields (Req 1 AC1)
            'first_name'       => ['required', 'string', 'max:255'],
            'middle_name'      => ['nullable', 'string', 'max:255'],
            'last_name'        => ['required', 'string', 'max:255'],
            'birth_date'       => ['required', 'date', 'before:today', function ($attr, $val, $fail) {
                // Minimum age 15 (Req 1 AC9)
                if (\Carbon\Carbon::parse($val)->age < 15) {
                    $fail('You must be at least 15 years old to register.');
                }
            }],
            'gender'           => ['required', 'in:male,female'],
            'contact_number'   => ['required', 'string', 'min:7', 'max:15'],
            'address'          => ['required', 'string', 'max:500'],

            // Government ID upload (Req 1 AC2)
            'id_files'         => ['required', 'array', 'min:1', 'max:3'],
            'id_files.*'       => ['file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'id_type'          => ['required', 'in:philsys,drivers_license,umid,passport,other'],
        ], [
            'id_files.required'    => 'Please upload at least one government-issued ID.',
            'id_files.min'         => 'Please upload at least one government-issued ID.',
            'id_files.max'         => 'You may upload a maximum of 3 ID files.',
            'id_files.*.file'      => 'One or more uploads are not valid files. Please re-select and try again.',
            'id_files.*.uploaded'  => 'One or more files could not be uploaded. Please re-select your ID file(s) and try again.',
            'id_files.*.mimes'     => 'ID files must be JPEG, PNG, or PDF format only.',
            'id_files.*.max'       => 'Each ID file must not exceed 5 MB.',
            'id_type.required'     => 'Please select the type of government ID you are uploading.',
        ]);

        DB::transaction(function () use ($request) {
            // Create user with pending_verification status (Req 1 AC4)
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role'     => 'resident',
                'status'   => 'pending_verification',
            ]);

            // Create resident profile with submitted fields
            $resident = Resident::create([
                'user_id'        => $user->id,
                'email'          => $request->email,
                'first_name'     => $request->first_name,
                'middle_name'    => $request->middle_name,
                'last_name'      => $request->last_name,
                'birth_date'     => $request->birth_date,
                'gender'         => $request->gender,
                'contact_number' => $request->contact_number,
                'address'        => $request->address,
                'source'         => 'self_registration',
            ]);

            // Store each uploaded ID file to Cloudinary (Req 1 AC5)
            $cloudinary = app(CloudinaryService::class);
            foreach ($request->file('id_files', []) as $file) {
                if (! $file || ! $file->isValid()) {
                    continue;
                }

                $uploaded = $cloudinary->upload($file, 'resident-documents');

                ResidentDocument::create([
                    'user_id'       => $user->id,
                    'resident_id'   => $resident->id,
                    'document_type' => $request->id_type,
                    'file_path'     => $uploaded['public_id'],
                    'cloudinary_url'=> $uploaded['secure_url'],
                    'original_name' => $uploaded['original_name'],
                    'mime_type'     => $uploaded['mime_type'],
                    'file_size'     => $uploaded['file_size'],
                    'context'       => 'registration',
                ]);
            }

            // Send confirmation email to registrant (Req 1 AC6)
            $user->notify(new AccountPendingNotification());
        });

        // Do NOT log them in — account is pending (Req 1 AC4)
        // Show confirmation message in UI (Req 1 AC8)
        return redirect()->route('login')
            ->with('success', 'Your account has been submitted for review. You will be notified by email once it has been approved.');
    }
}
