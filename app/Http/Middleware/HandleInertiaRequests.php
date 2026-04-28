<?php
// app/Http/Middleware/HandleInertiaRequests.php

namespace App\Http\Middleware;

use App\Http\Resources\Job\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Shared data available in EVERY Vue page component as `$page.props`
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [

            // ── Auth ──────────────────────────────────────────────────────
            'auth' => [
                'user' => $request->user() ? [
                    'id'        => $request->user()->id,
                    'name'      => $request->user()->name,
                    'email'     => $request->user()->email,
                    'user_type' => $request->user()->user_type,
                    'avatar'    => $request->user()->avatar,
                ] : null,
            ],

            // ── Flash Messages ────────────────────────────────────────────
            'flash' => [
                'success' => fn() => $request->session()->get('success'),
                'error'   => fn() => $request->session()->get('error'),
                'info'    => fn() => $request->session()->get('info'),
            ],

            // ── Global App Data ───────────────────────────────────────────
            'categories' => fn() => cache()->remember('nav_categories', 3600, function () {
                return Category::where('is_active', true)
                    ->whereNull('parent_id')
                    ->orderBy('sort_order')
                    ->get(['id', 'name', 'slug', 'job_count']);
            }),

            'appName' => config('app.name', 'MyJobs'),
        ]);
    }
}
