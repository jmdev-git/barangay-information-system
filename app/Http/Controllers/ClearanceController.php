<?php

namespace App\Http\Controllers;

use App\Models\Clearance;
use App\Models\Resident;
use App\Notifications\ClearanceStatusNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ClearanceController extends Controller
{
    // -------------------------------------------------------------------------
    // Helper — get the authenticated resident profile
    // -------------------------------------------------------------------------
    private function residentOrAbort(): Resident
    {
        $resident = Auth::user()?->resident;
        if (! $resident) {
            abort(403, 'Resident profile not found.');
        }
        return $resident;
    }

    // -------------------------------------------------------------------------
    // Resident — list own clearances (Req 5 AC3)
    // -------------------------------------------------------------------------
    public function index(): View
    {
        $clearances = $this->residentOrAbort()
            ->clearances()
            ->latest('requested_at')
            ->paginate(10);

        return view('resident.clearances.index', compact('clearances'));
    }

    // -------------------------------------------------------------------------
    // Resident — show request form
    // -------------------------------------------------------------------------
    public function create(): View
    {
        // Block if a pending request already exists (Req 5 AC6)
        $hasPending = $this->residentOrAbort()
            ->clearances()
            ->where('status', 'pending')
            ->exists();

        return view('resident.clearances.create', compact('hasPending'));
    }

    // -------------------------------------------------------------------------
    // Resident — submit request (Req 5 AC1, AC2, AC6)
    // -------------------------------------------------------------------------
    public function store(Request $request): RedirectResponse
    {
        $resident = $this->residentOrAbort();

        // Req 5 AC6 — block duplicate pending
        if ($resident->clearances()->where('status', 'pending')->exists()) {
            return back()->with('error', 'You already have a pending clearance request. Please wait for it to be reviewed before submitting another.');
        }

        $validated = $request->validate([
            'purpose' => 'required|string|min:10|max:500',
        ]);

        $resident->clearances()->create([
            'purpose'      => $validated['purpose'],
            'status'       => 'pending',
            'requested_at' => now(),
        ]);

        return redirect()->route('clearances.index')
            ->with('success', 'Clearance request submitted. You will be notified once it has been reviewed.');
    }

    // -------------------------------------------------------------------------
    // Resident — view one clearance
    // -------------------------------------------------------------------------
    public function show(Clearance $clearance): View
    {
        $user = Auth::user();

        if ($user->isResident()) {
            $resident = $this->residentOrAbort();
            if ((int) $clearance->resident_id !== (int) $resident->id) {
                abort(403);
            }
        }

        return view('resident.clearances.show', compact('clearance'));
    }

    // -------------------------------------------------------------------------
    // Resident — download approved certificate PDF (Req 5 AC4)
    // -------------------------------------------------------------------------
    public function download(Clearance $clearance)
    {
        $user = Auth::user();

        // Only the owning resident or admin may download
        if ($user->isResident()) {
            $resident = $this->residentOrAbort();
            if ((int) $clearance->resident_id !== (int) $resident->id) {
                abort(403);
            }
        }

        if ($clearance->status !== 'approved') {
            return back()->with('error', 'Clearance certificate is only available for approved requests.');
        }

        $clearance->load('resident.household');

        $pdf = Pdf::loadView('pdf.clearance-certificate', [
            'clearance' => $clearance,
            'resident'  => $clearance->resident,
            'issuedAt'  => now(),
        ])->setPaper('a4', 'portrait');

        $filename = 'barangay-clearance-' . $clearance->id . '-' . now()->format('Ymd') . '.pdf';

        return $pdf->download($filename);
    }

    // -------------------------------------------------------------------------
    // Admin — manage all clearances
    // -------------------------------------------------------------------------
    public function adminIndex(Request $request): View
    {
        $search = $request->query('search');
        $status = $request->query('status');

        $clearances = Clearance::with('resident')
            ->when($search, fn ($q) =>
                $q->where('purpose', 'like', "%{$search}%")
                  ->orWhereHas('resident', fn ($rq) =>
                        $rq->where('first_name', 'like', "%{$search}%")
                           ->orWhere('last_name',  'like', "%{$search}%"))
            )
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest('requested_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.clearances.index', compact('clearances', 'search', 'status'));
    }

    // -------------------------------------------------------------------------
    // Admin — approve (Req 5 AC4)
    // -------------------------------------------------------------------------
    public function approve(Clearance $clearance): RedirectResponse
    {
        $clearance->update([
            'status'      => 'approved',
            'issued_at'   => now(),
            'approved_by' => auth()->id(),
        ]);

        // Notify resident (Req 5 AC4 — certificate available for download)
        $clearance->resident?->user?->notify(
            new ClearanceStatusNotification($clearance, 'approved')
        );

        return back()->with('success', 'Clearance approved. The resident can now download the certificate.');
    }

    // -------------------------------------------------------------------------
    // Admin — reject (Req 5 AC5)
    // -------------------------------------------------------------------------
    public function reject(Request $request, Clearance $clearance): RedirectResponse
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:1|max:500',
        ]);

        $clearance->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'approved_by'      => auth()->id(),
        ]);

        // Email notification with reason (Req 5 AC5)
        $clearance->resident?->user?->notify(
            new ClearanceStatusNotification($clearance, 'rejected', $request->rejection_reason)
        );

        return back()->with('success', 'Clearance rejected and resident notified.');
    }
}
