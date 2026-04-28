<?php
namespace App\Http\Controllers\JobSeeker;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class ResumeController extends Controller
{
    public function show(): Response
    {
        $profile = auth()->user()
            ->jobSeekerProfile()
            ->with(['user:id,name,email,phone,avatar', 'educations', 'workExperiences', 'skills'])
            ->firstOrFail();

        return Inertia::render('JobSeeker/Resume', ['profile' => $profile]);
    }

    public function pdf()
    {
        $profile = auth()->user()
            ->jobSeekerProfile()
            ->with(['user:id,name,email,phone', 'educations', 'workExperiences', 'skills'])
            ->firstOrFail();

        $html = view('resume.pdf', compact('profile'))->render();
        $pdf  = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)->setPaper('a4');
        $name = \Illuminate\Support\Str::slug($profile->user->name) . '-resume.pdf';

        return $pdf->download($name);
    }
}
