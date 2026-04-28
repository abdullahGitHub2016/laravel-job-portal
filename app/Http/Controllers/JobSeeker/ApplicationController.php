<?php
namespace App\Http\Controllers\JobSeeker;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\JobPost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ApplicationController extends Controller
{
    private function seekerProfile()
    {
        return auth()->user()->jobSeekerProfile;
    }

    public function index(): Response
    {
        $applications = $this->seekerProfile()
            ->applications()
            ->with(['jobPost.employerProfile:id,company_name,logo'])
            ->orderByDesc('created_at')
            ->paginate(10);

        return Inertia::render('JobSeeker/Applications', ['applications' => $applications]);
    }

    public function store(Request $request, JobPost $job): RedirectResponse
    {
        $request->validate([
            'cover_letter'    => ['nullable', 'string', 'max:3000'],
            'expected_salary' => ['nullable', 'numeric', 'min:0'],
        ]);

        $seeker = $this->seekerProfile();

        $exists = Application::where('job_post_id', $job->id)
            ->where('job_seeker_profile_id', $seeker->id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'You have already applied for this job.');
        }

        Application::create([
            'job_post_id'           => $job->id,
            'job_seeker_profile_id' => $seeker->id,
            'cover_letter'          => $request->cover_letter,
            'expected_salary'       => $request->expected_salary,
        ]);

        $job->increment('application_count');

        return back()->with('success', 'Application submitted successfully!');
    }

    public function withdraw(Application $application): RedirectResponse
    {
        $application->update(['status' => 'withdrawn']);
        $application->jobPost->decrement('application_count');
        return back()->with('success', 'Application withdrawn.');
    }

    public function save(JobPost $job): RedirectResponse
    {
        $this->seekerProfile()->savedJobs()->syncWithoutDetaching([$job->id]);
        return back()->with('success', 'Job saved!');
    }

    public function unsave(JobPost $job): RedirectResponse
    {
        $this->seekerProfile()->savedJobs()->detach($job->id);
        return back();
    }
}
