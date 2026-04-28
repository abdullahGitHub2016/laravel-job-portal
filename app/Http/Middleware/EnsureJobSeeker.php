<?php
// app/Http/Middleware/EnsureJobSeeker.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureJobSeeker
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->isJobSeeker()) {
            return redirect()->route('home')
                ->with('error', 'This area is for Job Seekers only.');
        }

        // Bounce back to profile setup if profile not yet created
        if (! $request->user()->jobSeekerProfile && ! $request->routeIs('seeker.profile.*')) {
            return redirect()->route('seeker.profile.edit')
                ->with('info', 'Please complete your profile first.');
        }

        return $next($request);
    }
}
