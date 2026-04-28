<?php
namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $profile = auth()->user()->employerProfile;

        return Inertia::render('Employer/Dashboard', [
            'stats' => [
                'total_jobs'        => $profile?->jobPosts()->count() ?? 0,
                'active_jobs'       => $profile?->activeJobPosts()->count() ?? 0,
                'total_applicants'  => $profile?->jobPosts()->withCount('applications')->get()->sum('applications_count') ?? 0,
            ],
            'recentJobs' => $profile?->jobPosts()->withCount('applications')->orderByDesc('created_at')->limit(5)->get() ?? collect(),
        ]);
    }
}
