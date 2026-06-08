<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAccountStatus
{
    /**
     * Block resident routes based on real account status (Req 3 AC5, AC6).
     *
     * - pending_verification → redirect to pending info page
     * - rejected             → redirect to rejected info page with reason
     * - active               → pass through
     *
     * Admin accounts always bypass this check (they should always be active).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // Admins are not subject to resident account status checks
        if ($user->isAdmin()) {
            return $next($request);
        }

        if ($user->isPendingVerification()) {
            return redirect()->route('account.pending');
        }

        if ($user->isRejected()) {
            return redirect()->route('account.rejected');
        }

        return $next($request);
    }
}
