<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('resend-invitation', function ($request) {
            $userKey = optional($request->user())->getAuthIdentifier() ?: $request->ip();
            $invKey = $request->route('invitation');

            return [
                // Global safety: 5 resends / 10 min per user
                Limit::perMinutes(10, 5)->by('user:'.$userKey),
                // Per-invitation: 1 resend / 5 min per invitation
                Limit::perMinutes(5, 1)->by('inv:'.$invKey),
            ];
        });
    }
}
