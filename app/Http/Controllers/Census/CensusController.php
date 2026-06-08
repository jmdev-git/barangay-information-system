<?php

namespace App\Http\Controllers\Census;

use App\Http\Controllers\Controller;
use App\Models\Household;
use App\Models\Resident;
use App\Models\ResidentDocument;
use App\Models\User;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CensusController extends Controller
{
    /**
     * Census workflow session key.
     * We store step data in session so the user can move forward
     * and back without losing progress.
     */
    private const SESSION_KEY = 'census_data';

    // -------------------------------------------------------------------------
    // Census overview page
    // -------------------------------------------------------------------------

    public function index()
    {
        return view('census.index');
    }

    // -------------------------------------------------------------------------
    // Step 1 — Search or Create Household (Diagram Step 2)
    // -------------------------------------------------------------------------

    public function step1(Request $request)
    {
        $search     = $request->get('q', '');
        $households = collect();

        if ($search) {
            $households = Household::with('head')
                ->where('address', 'like', "%{$search}%")
                ->orWhere('purok', 'like', "%{$search}%")
                ->orWhere('household_head_name', 'like', "%{$search}%")
                ->orderBy('address')
                ->paginate(10)
                ->withQueryString();
        }

        $censusData = session(self::SESSION_KEY, []);

        return view('census.step1', compact('households', 'search', 'censusData'));
    }

    /**
     * Save step 1: either select existing household or create new one inline.
     */
    public function step1Store(Request $request)
    {
        $action = $request->input('action', 'select');

        if ($action === 'select') {
            $request->validate([
                'household_id' => 'required|exists:households,id',
            ]);
            $household = Household::findOrFail($request->household_id);
        } else {
            // Create new household inline
            $request->validate([
                'new_address'            => 'required|string|max:500',
                'new_purok'              => 'nullable|string|max:100',
                'new_barangay'           => 'required|string|max:100',
                'new_household_head_name'=> 'nullable|string|max:255',
            ]);

            $household = Household::create([
                'address'             => $request->new_address,
                'purok'               => $request->new_purok,
                'barangay'            => $request->new_barangay,
                'household_head_name' => $request->new_household_head_name,
            ]);
        }

        // Save to session
        $data = session(self::SESSION_KEY, []);
        $data['household_id']   = $household->id;
        $data['household_info'] = [
            'address' => $household->address,
            'purok'   => $household->purok,
            'barangay'=> $household->barangay,
        ];
        session([self::SESSION_KEY => $data]);

        return redirect()->route('census.step2');
    }

    // -------------------------------------------------------------------------
    // Step 2 — Collect Resident Information (Diagram Step 3)
    // -------------------------------------------------------------------------

    public function step2()
    {
        $data = session(self::SESSION_KEY, []);
        if (empty($data['household_id'])) {
            return redirect()->route('census.step1')
                ->with('error', 'Please select a household first.');
        }

        $household = Household::find($data['household_id']);

        return view('census.step2', compact('data', 'household'));
    }

    public function step2Store(Request $request)
    {
        $data = session(self::SESSION_KEY, []);
        if (empty($data['household_id'])) {
            return redirect()->route('census.step1');
        }

        $validated = $request->validate([
            'first_name'           => 'required|string|max:255',
            'middle_name'          => 'nullable|string|max:255',
            'last_name'            => 'required|string|max:255',
            'birth_date'           => 'required|date|before:today',
            'gender'               => 'required|in:male,female',
            'civil_status'         => 'required|in:single,married,widowed,separated,annulled',
            'relationship_to_head' => 'nullable|string|max:100',
            'contact_number'       => 'required|string|min:7|max:15',
        ]);

        $data['resident_info'] = $validated;
        session([self::SESSION_KEY => $data]);

        return redirect()->route('census.step3');
    }

    // -------------------------------------------------------------------------
    // Step 3 — Capture / Upload Valid ID + Photo (Diagram Step 4)
    // -------------------------------------------------------------------------

    public function step3()
    {
        $data = session(self::SESSION_KEY, []);
        if (empty($data['resident_info'])) {
            return redirect()->route('census.step2');
        }

        return view('census.step3', [
            'documentTypes' => ResidentDocument::DOCUMENT_TYPES,
        ]);
    }

    public function step3Store(Request $request)
    {
        $data = session(self::SESSION_KEY, []);
        if (empty($data['resident_info'])) {
            return redirect()->route('census.step2');
        }

        $request->validate([
            'id_files'      => 'required|array|min:1|max:3',
            'id_files.*'    => 'required|file|mimes:jpeg,png,pdf|max:5120', // 5 MB
            'id_type'       => 'required|in:philsys,drivers_license,umid,passport,other',
            'resident_photo'=> 'nullable|file|mimes:jpeg,png|max:5120',     // Req 8 AC5
        ]);

        // Upload ID files to Cloudinary (Req 1 AC5 — secure cloud storage)
        $cloudinary  = app(CloudinaryService::class);
        $storedFiles = [];
        foreach ($request->file('id_files') as $file) {
            $uploaded = $cloudinary->upload($file, 'resident-documents');
            $storedFiles[] = [
                'path'          => $uploaded['public_id'],
                'cloudinary_url'=> $uploaded['secure_url'],
                'original_name' => $uploaded['original_name'],
                'mime_type'     => $uploaded['mime_type'],
                'file_size'     => $uploaded['file_size'],
                'document_type' => $request->id_type,
            ];
        }

        // Upload resident photo to Cloudinary (optional, for census only)
        $photoPath = null;
        if ($request->hasFile('resident_photo')) {
            $uploaded  = $cloudinary->upload($request->file('resident_photo'), 'resident-photos');
            $photoPath = $uploaded['secure_url'];
        }

        $data['id_files']   = $storedFiles;
        $data['id_type']    = $request->id_type;
        $data['photo_path'] = $photoPath;
        session([self::SESSION_KEY => $data]);

        return redirect()->route('census.step4');
    }

    // -------------------------------------------------------------------------
    // Step 4 — Validate Information (Diagram Step 5)
    // -------------------------------------------------------------------------

    public function step4()
    {
        $data = session(self::SESSION_KEY, []);
        if (empty($data['id_files'])) {
            return redirect()->route('census.step3');
        }

        $info      = $data['resident_info'];
        $household = Household::find($data['household_id']);

        // Duplicate check (Req 8 AC3)
        $duplicates = Resident::where('first_name', $info['first_name'])
            ->where('last_name', $info['last_name'])
            ->whereDate('birth_date', $info['birth_date'])
            ->with('household')
            ->get();

        return view('census.step4', compact('data', 'info', 'household', 'duplicates'));
    }

    /**
     * Enumerator confirms they want to proceed despite a duplicate warning.
     * We record a 'force_save' flag in session (Req 8 AC3).
     */
    public function step4Confirm(Request $request)
    {
        $data = session(self::SESSION_KEY, []);
        $data['confirmed'] = true;
        session([self::SESSION_KEY => $data]);

        return redirect()->route('census.step5.save');
    }

    // -------------------------------------------------------------------------
    // Step 5 — Create Profile + Save to DB (Diagram Steps 6 & 7)
    // -------------------------------------------------------------------------

    public function step5Save(Request $request)
    {
        $data = session(self::SESSION_KEY, []);

        if (empty($data['id_files']) || empty($data['resident_info'])) {
            return redirect()->route('census.step1')
                ->with('error', 'Session expired. Please start the census form again.');
        }

        $info      = $data['resident_info'];
        $household = Household::findOrFail($data['household_id']);

        // Final duplicate check — block if not confirmed (Req 8 AC3)
        $duplicates = Resident::where('first_name', $info['first_name'])
            ->where('last_name', $info['last_name'])
            ->whereDate('birth_date', $info['birth_date'])
            ->get();

        if ($duplicates->isNotEmpty() && empty($data['confirmed'])) {
            return redirect()->route('census.step4')
                ->with('error', 'Duplicate detected. Please confirm to continue.');
        }

        DB::transaction(function () use ($data, $info, $household, &$resident) {
            // Create the resident profile (Req 8 AC4 — status active, verified_by enumerator)
            $resident = Resident::create([
                'household_id'         => $household->id,
                'first_name'           => $info['first_name'],
                'middle_name'          => $info['middle_name'] ?? null,
                'last_name'            => $info['last_name'],
                'birth_date'           => $info['birth_date'],
                'gender'               => $info['gender'],
                'civil_status'         => $info['civil_status'],
                'relationship_to_head' => $info['relationship_to_head'] ?? null,
                'contact_number'       => $info['contact_number'],
                'address'              => $household->address,
                'photo_path'           => $data['photo_path'] ?? null,
                'verified_by'          => auth()->id(),  // enumerator (Req 8 AC4)
                'source'               => 'census',
            ]);

            // Save ID documents linked to the resident profile
            foreach ($data['id_files'] as $file) {
                ResidentDocument::create([
                    'resident_id'   => $resident->id,
                    'document_type' => $file['document_type'],
                    'file_path'     => $file['path'],
                    'cloudinary_url'=> $file['cloudinary_url'] ?? null,
                    'original_name' => $file['original_name'],
                    'mime_type'     => $file['mime_type'],
                    'file_size'     => $file['file_size'],
                    'context'       => 'census',
                ]);
            }
        });

        // Clear census session
        session()->forget(self::SESSION_KEY);

        return redirect()->route('census.complete', $resident->id)
            ->with('success', 'Resident profile created and saved to the BMIS database.');
    }

    // -------------------------------------------------------------------------
    // Step 6 — Complete / Generate Reports link (Diagram Step 8)
    // -------------------------------------------------------------------------

    public function complete(Resident $resident)
    {
        $resident->load(['household', 'documents']);
        return view('census.complete', compact('resident'));
    }

    // -------------------------------------------------------------------------
    // Reset / Start Over
    // -------------------------------------------------------------------------

    public function reset()
    {
        session()->forget(self::SESSION_KEY);
        return redirect()->route('census.step1')
            ->with('info', 'Census form has been reset. You may start a new intake.');
    }
}
