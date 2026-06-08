<?php

namespace App\Http\Controllers;

use App\Models\Blotter;
use App\Models\Clearance;
use App\Models\Household;
use App\Models\Resident;
use App\Models\User;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        if ($user && $user->isAdmin()) {
            return view('admin.dashboard', [
                // Req 3 AC3: active residents (not just total)
                'activeResidents'    => User::where('role', 'resident')->where('status', 'active')->count(),
                'householdCount'     => Household::count(),
                // Open blotters (Req 3 AC3)
                'openBlotters'       => Blotter::where('status', 'open')->count(),
                // Clearance counts by status (Req 3 AC3)
                'pendingClearances'  => Clearance::where('status', 'pending')->count(),
                'approvedClearances' => Clearance::where('status', 'approved')->count(),
                'rejectedClearances' => Clearance::where('status', 'rejected')->count(),
                // Pending account approvals badge (Req 2)
                'pendingApprovals'   => User::where('role', 'resident')->where('status', 'pending_verification')->count(),
            ]);
        }

        // Resident dashboard — counts injected in the view via auth()->user()->resident
        return view('resident.dashboard');
    }
}
