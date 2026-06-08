<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountApprovalLog;
use App\Models\User;
use App\Notifications\AccountApprovedNotification;
use App\Notifications\AccountRejectedNotification;
use Illuminate\Http\Request;

class AccountApprovalController extends Controller
{
    /**
     * List all pending verification accounts (Req 2 AC1).
     */
    public function index()
    {
        $pendingUsers = User::with(['resident', 'resident.documents'])
            ->where('role', 'resident')
            ->where('status', 'pending_verification')
            ->latest()
            ->paginate(15);

        return view('admin.accounts.index', compact('pendingUsers'));
    }

    /**
     * Show one pending account with uploaded ID files (Req 2 AC1).
     */
    public function show(User $user)
    {
        $user->load(['resident', 'resident.documents']);

        // Also load documents linked directly to the user (pre-resident-profile creation)
        $documents = $user->resident
            ? $user->resident->documents
            : \App\Models\ResidentDocument::where('user_id', $user->id)->get();

        return view('admin.accounts.show', compact('user', 'documents'));
    }

    /**
     * Approve a pending account (Req 2 AC2).
     */
    public function approve(Request $request, User $user)
    {
        $user->update([
            'status'      => 'active',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        // Audit log (Req 2 AC5)
        AccountApprovalLog::create([
            'admin_id'       => auth()->id(),
            'target_user_id' => $user->id,
            'action'         => 'approved',
        ]);

        // Email notification (Req 2 AC2)
        $user->notify(new AccountApprovedNotification());

        return redirect()->route('admin.accounts.index')
            ->with('success', "Account for {$user->name} has been approved.");
    }

    /**
     * Reject a pending account (Req 2 AC3).
     */
    public function reject(Request $request, User $user)
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:1|max:500',
        ]);

        $user->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'approved_by'      => auth()->id(),
            'approved_at'      => now(),
        ]);

        // Audit log (Req 2 AC5)
        AccountApprovalLog::create([
            'admin_id'       => auth()->id(),
            'target_user_id' => $user->id,
            'action'         => 'rejected',
            'reason'         => $request->rejection_reason,
        ]);

        // Email notification with reason (Req 2 AC3)
        $user->notify(new AccountRejectedNotification($request->rejection_reason));

        return redirect()->route('admin.accounts.index')
            ->with('success', "Account for {$user->name} has been rejected.");
    }
}
