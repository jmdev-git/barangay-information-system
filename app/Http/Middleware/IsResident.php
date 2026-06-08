<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsResident
{
    /**
     * Only allow resident-role users through.
     * Used on resident self-service routes.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('login');
        }

        if (! $request->user()->isResident()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Forbidden. Resident access only.'], 403);
            }
            abort(403, 'Resident access only.');
        }

        return $next($request);
    }
}
