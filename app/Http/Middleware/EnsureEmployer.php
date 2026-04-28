<?php
// app/Http/Middleware/EnsureEmployer.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmployer
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->isEmployer()) {
            return redirect()->route('home')
                ->with('error', 'This area is for Employers only.');
        }

        if (! $request->user()->employerProfile && ! $request->routeIs('employer.company.*')) {
            return redirect()->route('employer.company.edit')
                ->with('info', 'Please set up your company profile first.');
        }

        return $next($request);
    }
}


// ──────────────────────────────────────────────────────────────
// bootstrap/app.php  (Laravel 11 — add inside withMiddleware)
// ──────────────────────────────────────────────────────────────
/*
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'seeker'   => \App\Http\Middleware\EnsureJobSeeker::class,
        'employer' => \App\Http\Middleware\EnsureEmployer::class,
    ]);

    // Share auth data globally with Inertia
    $middleware->web(append: [
        \App\Http\Middleware\HandleInertiaRequests::class,
    ]);
})
*/
