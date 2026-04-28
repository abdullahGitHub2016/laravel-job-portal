<?php
namespace App\Http\Controllers\JobSeeker;

use App\Http\Controllers\Controller;
use App\Http\Resources\Job\JobResource;
use App\Models\JobPost;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $profile = auth()->user()->jobSeekerProfile;

        $recentApplications = $profile
            ? $profile->applications()
                ->with(['jobPost.employerProfile:id,company_name,logo'])
                ->orderByDesc('created_at')
                ->limit(5)
                ->get()
            : collect();

        $recommendedJobs = JobPost::with('employerProfile:id,company_name,logo')
            ->published()
            ->inRandomOrder()
            ->limit(6)
            ->get();

        return Inertia::render('JobSeeker/Dashboard', [
            'stats' => [
                'applications' => $profile?->applications()->count() ?? 0,
                'saved'        => $profile?->savedJobs()->count() ?? 0,
                'interviews'   => $profile?->applications()->where('status', 'interview')->count() ?? 0,
                'profile_views'=> 0,
            ],
            'recentApplications' => $recentApplications,
            'recommendedJobs'    => JobResource::collection($recommendedJobs),
        ]);
    }
}
