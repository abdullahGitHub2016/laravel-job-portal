<?php

namespace App\Http\Controllers\Jobs;

use App\Http\Controllers\Controller;
use App\Http\Resources\Job\JobCollection;
use App\Http\Resources\Job\JobResource;
use App\Models\Application;
use App\Models\Category;
use App\Models\JobPost;
use App\Models\SearchLog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class JobController extends Controller
{
    public function index(Request $request): Response
    {
        $validated = $request->validate([
            'q'          => ['nullable', 'string', 'max:100'],
            'category'   => ['nullable', 'string'],
            'location'   => ['nullable', 'string', 'max:100'],
            'job_type'   => ['nullable', 'in:full_time,part_time,contract,internship,remote,hybrid'],
            'salary_min' => ['nullable', 'numeric', 'min:0'],
            'salary_max' => ['nullable', 'numeric', 'min:0'],
            'experience' => ['nullable', 'in:entry,junior,mid,senior,lead,executive'],
            'sort'       => ['nullable', 'in:latest,salary_high,salary_low,deadline'],
            'page'       => ['nullable', 'integer', 'min:1'],
        ]);

        $jobs = JobPost::query()
            ->with(['employerProfile:id,company_name,logo,district', 'category:id,name,slug'])
            ->published()
            ->when($validated['q'] ?? null, fn($q, $term) => $q->search($term))
            ->when(
                $validated['category'] ?? null,
                fn($q, $slug) =>
                $q->whereHas('category', fn($c) => $c->where('slug', $slug))
            )
            ->when(
                $validated['location'] ?? null,
                fn($q, $loc) =>
                $q->where('district', 'like', "%{$loc}%")
            )
            ->when(
                $validated['job_type'] ?? null,
                fn($q, $type) =>
                $q->where('job_type', $type)
            )
            ->when(
                $validated['salary_min'] ?? null,
                fn($q, $min) =>
                $q->where('salary_max', '>=', $min)
            )
            ->when(
                $validated['salary_max'] ?? null,
                fn($q, $max) =>
                $q->where('salary_min', '<=', $max)
            )
            ->when(
                $validated['experience'] ?? null,
                fn($q, $level) =>
                $q->where('experience_level', $level)
            )
            ->when(($validated['sort'] ?? 'latest') === 'latest',     fn($q) => $q->orderByDesc('published_at'))
            ->when(($validated['sort'] ?? '') === 'salary_high',       fn($q) => $q->orderByDesc('salary_max'))
            ->when(($validated['sort'] ?? '') === 'salary_low',        fn($q) => $q->orderBy('salary_min'))
            ->when(($validated['sort'] ?? '') === 'deadline',          fn($q) => $q->orderBy('application_deadline'))
            ->orderByDesc('is_featured')
            ->orderByDesc('is_hot')
            ->paginate(15)
            ->withQueryString();

        if (! empty($validated['q'])) {
            SearchLog::create([
                'user_id'       => auth()->id(),
                'session_id'    => session()->getId(),
                'query'         => $validated['q'],
                'location'      => $validated['location'] ?? null,
                'job_type'      => $validated['job_type'] ?? null,
                'results_count' => $jobs->total(),
                'ip_address'    => $request->ip(),
                'created_at'    => now(),
            ]);
        }

        return Inertia::render('Jobs/Index', [
            'jobs'       => new JobCollection($jobs),
            'filters'    => $validated,
            'categories' => Category::where('is_active', true)
                ->whereNull('parent_id')
                ->withCount(['jobPosts' => fn($q) => $q->published()])
                ->orderBy('sort_order')
                ->get(['id', 'name', 'slug']),
            'meta' => [
                'total'       => $jobs->total(),
                'currentPage' => $jobs->currentPage(),
            ],
        ]);
    }

    public function show(Request $request, JobPost $job): Response
    {
        abort_unless($job->status === 'published', 404);

        JobPost::where('id', $job->id)->increment('view_count');
        $job->refresh();

        $job->loadMissing([
            'employerProfile' => fn($q) => $q->with('industry'),
            'category',
            'skills',
        ]);

        $hasApplied = $hasSaved = false;
        $applicationStatus = null;

        if (auth()->check() && auth()->user()->isJobSeeker()) {
            $seekerProfile = auth()->user()->jobSeekerProfile;
            if ($seekerProfile) {
                $application       = Application::where('job_post_id', $job->id)
                    ->where('job_seeker_profile_id', $seekerProfile->id)
                    ->first();
                $hasApplied        = (bool) $application;
                $applicationStatus = $application?->status;
                $hasSaved          = $seekerProfile->savedJobs()->where('job_post_id', $job->id)->exists();
            }
        }

        $relatedJobs = JobPost::with('employerProfile:id,company_name,logo,district')
            ->published()
            ->where('category_id', $job->category_id)
            ->where('id', '!=', $job->id)
            ->orderByDesc('published_at')
            ->limit(4)->get();

        $jobData               = (new JobResource($job))->resolve();
        $jobData['id']         = (string) $job->getKey();

        return Inertia::render('Jobs/Show', [
            'job'               => $jobData,
            'relatedJobs'       => JobResource::collection($relatedJobs)->resolve(),
            'hasApplied'        => $hasApplied,
            'applicationStatus' => $applicationStatus,
            'hasSaved'          => $hasSaved,
        ]);
    }
}
