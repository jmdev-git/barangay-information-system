<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        /**
         * API Rate Limits (Req 9 AC6):
         *  - Authenticated users: 60 req/min per user ID
         *  - Unauthenticated IPs: 30 req/min per IP
         */
        RateLimiter::for('api', function (Request $request) {
            if ($request->user()) {
                return Limit::perMinute(60)
                    ->by($request->user()->id)
                    ->response(function (Request $request, array $headers) {
                        return response()->json([
                            'message'     => 'Too many requests.',
                            'retry_after' => $headers['Retry-After'] ?? 60,
                        ], 429, $headers);
                    });
            }

            return Limit::perMinute(30)
                ->by($request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'message'     => 'Too many requests.',
                        'retry_after' => $headers['Retry-After'] ?? 60,
                    ], 429, $headers);
                });
        });
    }
}
