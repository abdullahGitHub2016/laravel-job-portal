<?php
namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\JobPost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ApplicantController extends Controller
{
    public function index(JobPost $job): Response
    {
        $applicants = $job->applications()
            ->with(['jobSeekerProfile' => fn($q) => $q->with([
                'user:id,name,email',
                'skills:id,name',
                'educations',
            ])])
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('status');

        return Inertia::render('Employer/Applicants/Index', [
            'job'        => $job->only('id', 'title', 'application_deadline'),
            'applicants' => $applicants,
            'statuses'   => ['pending','reviewed','shortlisted','interview','offered','hired','rejected'],
        ]);
    }

    public function show(JobPost $job, Application $application): Response
    {
        $application->load(['jobSeekerProfile' => fn($q) => $q->with([
            'user:id,name,email,phone',
            'skills',
            'educations',
            'workExperiences',
        ])]);

        if ($application->status === 'pending') {
            $application->update(['status' => 'reviewed', 'reviewed_at' => now(), 'reviewed_by' => auth()->id()]);
        }

        return Inertia::render('Employer/Applicants/Show', [
            'job'         => $job->only('id', 'title'),
            'application' => $application,
        ]);
    }

    public function updateStatus(Request $request, JobPost $job, Application $application): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:reviewed,shortlisted,interview,offered,hired,rejected',
            'notes'  => 'nullable|string|max:1000',
        ]);

        $application->update([
            'status'         => $request->status,
            'employer_notes' => $request->notes ?? $application->employer_notes,
        ]);

        return back()->with('success', 'Applicant status updated.');
    }

    public function downloadResume(Application $application)
    {
        return \Storage::download($application->resume_snapshot);
    }
}
