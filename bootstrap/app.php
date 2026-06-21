<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);

        // The app has no 'login' route (only 'admin.login'); without this, the
        // framework's auth middleware 500s on guest access to admin routes.
        $middleware->redirectGuestsTo(fn () => route('admin.login'));

        // Trust the load balancer / reverse proxy so $request->secure(), HSTS,
        // secure cookies, and the real client IP work behind TLS termination.
        // Default '*' suits a cloud LB with dynamic IPs; if the app is also
        // directly reachable, set TRUSTED_PROXIES to the proxy CIDR to stop
        // X-Forwarded-* spoofing (which would also forge the login throttle IP).
        $proxies = env('TRUSTED_PROXIES', '*');
        $middleware->trustProxies(
            at: $proxies === '*' ? '*' : array_map('trim', explode(',', $proxies)),
            headers: Request::HEADER_X_FORWARDED_FOR
                | Request::HEADER_X_FORWARDED_HOST
                | Request::HEADER_X_FORWARDED_PORT
                | Request::HEADER_X_FORWARDED_PROTO
                | Request::HEADER_X_FORWARDED_AWS_ELB,
        );

        // Apply security headers (CSP, HSTS, X-Frame-Options, ...) to all web responses.
        $middleware->web(append: [
            \App\Http\Middleware\SecurityHeaders::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Report exceptions to Sentry when SENTRY_LARAVEL_DSN is set; a no-op otherwise.
        \Sentry\Laravel\Integration::handles($exceptions);
    })->create();
