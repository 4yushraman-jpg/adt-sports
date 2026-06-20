<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Use Tailwind-compatible pagination views (we handle styling via CSS)
        Paginator::useBootstrapFive();

        // Throttle admin login: 5 attempts/min per email+IP to block brute force
        // without letting one IP lock out every account.
        RateLimiter::for('login', function (Request $request) {
            $key = Str::lower((string) $request->input('email')) . '|' . $request->ip();
            return Limit::perMinute(5)->by($key);
        });
    }
}
