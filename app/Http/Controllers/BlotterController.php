<?php

namespace App\Http\Controllers;

use App\Models\Blotter;
use App\Notifications\BlotterStatusNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlotterController extends Controller
{
    // -------------------------------------------------------------------------
    // Index — all blotters with search + status filter (Req 7 AC1, AC2)
    // -------------------------------------------------------------------------
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $status = $request->query('status');

        $blotters = Blotter::with(['complainant', 'respondent'])
            ->when($search, fn ($q) =>
                $q->where('complainant_name',    'like', "%{$search}%")
                  ->orWhere('respondent_name',   'like', "%{$search}%")
                  ->orWhere('incident_description','like', "%{$search}%")
                  ->orWhere('location',           'like', "%{$search}%")
                  ->orWhere('case_number',        'like', "%{$search}%")
            )
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.blotters.index', compact('blotters', 'search', 'status'));
    }

    // -------------------------------------------------------------------------
    // Create
    // -------------------------------------------------------------------------
    public function create(): View
    {
        return view('admin.blotters.create');
    }

    // -------------------------------------------------------------------------
    // Store — admin direct entry (Req 7 AC4, AC5)
    // -------------------------------------------------------------------------
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'complainant_name'     => 'required|string|min:1|max:255',
            'respondent_name'      => 'required|string|min:1|max:255',
            'incident_date'        => 'required|date|before_or_equal:today',
            'incident_description' => 'required|string|min:1|max:1000',
            'location'             => 'required|string|min:1|max:255',
        ]);

        // Req 7 AC5 — complainant ≠ respondent (case-insensitive)
        if (strtolower($validated['complainant_name']) === strtolower($validated['respondent_name'])) {
            return back()->withInput()
                ->withErrors(['respondent_name' => 'The complainant and respondent cannot be the same person.']);
        }

        Blotter::create(array_merge($validated, ['status' => 'open']));

        return redirect()->route('blotters.index')
            ->with('success', 'Blotter record created successfully.');
    }

    // -------------------------------------------------------------------------
    // Show (Req 7 AC1)
    // -------------------------------------------------------------------------
    public function show(Blotter $blotter): View
    {
        $blotter->load(['complainant', 'respondent']);
        return view('admin.blotters.show', compact('blotter'));
    }

    // -------------------------------------------------------------------------
    // Edit
    // -------------------------------------------------------------------------
    public function edit(Blotter $blotter): View
    {
        return view('admin.blotters.edit', compact('blotter'));
    }

    // -------------------------------------------------------------------------
    // Update — validate status values (Req 7 AC3, AC6)
    // -------------------------------------------------------------------------
    public function update(Request $request, Blotter $blotter): RedirectResponse
    {
        $validated = $request->validate([
            'complainant_name'     => 'required|string|min:1|max:255',
            'respondent_name'      => 'required|string|min:1|max:255',
            'incident_date'        => 'required|date|before_or_equal:today',
            'incident_description' => 'required|string|min:1|max:1000',
            'location'             => 'required|string|min:1|max:255',
            // Req 7 AC3 — only these four values accepted
            'status'               => 'required|in:pending_review,open,closed,resolved',
        ]);

        // Req 7 AC5 — complainant ≠ respondent
        if (strtolower($validated['complainant_name']) === strtolower($validated['respondent_name'])) {
            return back()->withInput()
                ->withErrors(['respondent_name' => 'The complainant and respondent cannot be the same person.']);
        }

        $blotter->update($validated);

        return redirect()->route('blotters.show', $blotter)
            ->with('success', 'Blotter record updated successfully.');
    }

    // -------------------------------------------------------------------------
    // Destroy
    // -------------------------------------------------------------------------
    public function destroy(Blotter $blotter): RedirectResponse
    {
        $blotter->delete();
        return redirect()->route('blotters.index')
            ->with('success', 'Blotter record deleted.');
    }

    // -------------------------------------------------------------------------
    // Approve resident-submitted blotter (Req 7 AC — open status)
    // -------------------------------------------------------------------------
    public function approve(Blotter $blotter): RedirectResponse
    {
        if ($blotter->status !== 'pending_review') {
            return back()->with('error', 'Only pending blotters can be approved.');
        }

        $blotter->update(['status' => 'open']);

        // Notify filing resident (Req 6 AC3)
        $blotter->complainant?->user?->notify(
            new BlotterStatusNotification($blotter, 'approved')
        );

        return back()->with('success', "Blotter {$blotter->case_number} approved and set to Open.");
    }

    // -------------------------------------------------------------------------
    // Reject resident-submitted blotter (Req 6 AC4, Req 7)
    // -------------------------------------------------------------------------
    public function reject(Request $request, Blotter $blotter): RedirectResponse
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:1|max:500',
        ]);

        if ($blotter->status !== 'pending_review') {
            return back()->with('error', 'Only pending blotters can be rejected.');
        }

        $blotter->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        // Notify filing resident (Req 6 AC4)
        $blotter->complainant?->user?->notify(
            new BlotterStatusNotification($blotter, 'rejected', $request->rejection_reason)
        );

        return back()->with('success', "Blotter {$blotter->case_number} rejected and resident notified.");
    }
}
