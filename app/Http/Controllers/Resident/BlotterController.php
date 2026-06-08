<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\Blotter;
use Illuminate\Http\Request;

class BlotterController extends Controller
{
    /**
     * List all blotter records filed by the authenticated resident (Req 6 AC5).
     */
    public function index()
    {
        $resident = auth()->user()->resident;

        if (! $resident) {
            return redirect()->route('dashboard')
                ->with('info', 'You do not have a resident profile yet.');
        }

        $blotters = $resident->complainantBlotters()
            ->latest()
            ->paginate(10);

        return view('resident.blotters.index', compact('blotters'));
    }

    /**
     * Show the blotter filing form (Req 6 AC1).
     */
    public function create()
    {
        return view('resident.blotters.create');
    }

    /**
     * Store a new resident-submitted blotter (Req 6 AC1, AC2).
     */
    public function store(Request $request)
    {
        $user     = auth()->user();
        $resident = $user->resident;

        if (! $resident) {
            return back()->with('error', 'Resident profile not found. Please contact the barangay admin.');
        }

        $validated = $request->validate([
            'respondent_name'      => 'required|string|max:255',
            'incident_date'        => 'required|date|before_or_equal:today',
            'incident_description' => 'required|string|max:2000',
            'location'             => 'required|string|max:500',
        ]);

        // Complainant cannot be respondent (Req 6 AC2)
        if (strtolower($resident->full_name) === strtolower($validated['respondent_name'])) {
            return back()
                ->withInput()
                ->withErrors(['respondent_name' => 'The complainant and respondent cannot be the same person.']);
        }

        Blotter::create([
            'complainant_id'       => $resident->id,
            'complainant_name'     => $resident->full_name,
            'respondent_id'        => null,
            'respondent_name'      => $validated['respondent_name'],
            'incident_date'        => $validated['incident_date'],
            'incident_description' => $validated['incident_description'],
            'location'             => $validated['location'],
            'status'               => 'pending_review', // Req 6 AC2
        ]);

        return redirect()->route('resident.blotters.index')
            ->with('success', 'Your blotter report has been submitted and is pending review by the barangay admin.');
    }

    /**
     * Show one blotter record filed by the resident (Req 6 AC5).
     */
    public function show(Blotter $blotter)
    {
        $resident = auth()->user()->resident;

        // Residents can only view their own blotters
        if (! $resident || $blotter->complainant_id !== $resident->id) {
            abort(403);
        }

        return view('resident.blotters.show', compact('blotter'));
    }
}
