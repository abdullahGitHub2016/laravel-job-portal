<?php
namespace App\Http\Controllers\JobSeeker;

use App\Http\Controllers\Controller;
use App\Models\Education;
use App\Models\Skill;
use App\Models\WorkExperience;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    private function profile()
    {
        return auth()->user()->jobSeekerProfile;
    }

    public function edit(): Response
    {
        $profile = $this->profile();

        if (! $profile) {
            $profile = auth()->user()->jobSeekerProfile()->create([
                'years_of_experience' => 0,
                'job_seeking_status'  => 'actively_looking',
                'is_profile_public'   => true,
            ]);
        }

        $profile->load(['educations', 'workExperiences', 'skills']);

        return Inertia::render('JobSeeker/Profile', [
            'profile'         => $profile,
            'availableSkills' => Skill::orderBy('name')->get(['id', 'name', 'category']),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'headline'            => ['nullable', 'string', 'max:150'],
            'bio'                 => ['nullable', 'string', 'max:2000'],
            'current_job_title'   => ['nullable', 'string', 'max:100'],
            'current_company'     => ['nullable', 'string', 'max:100'],
            'years_of_experience' => ['required', 'integer', 'min:0', 'max:50'],
            'district'            => ['nullable', 'string', 'max:100'],
            'gender'              => ['nullable', 'in:male,female,other'],
            'date_of_birth'       => ['nullable', 'date'],
            'expected_salary_min' => ['nullable', 'numeric', 'min:0'],
            'expected_salary_max' => ['nullable', 'numeric', 'min:0'],
            'job_seeking_status'  => ['required', 'in:actively_looking,open_to_offers,not_looking'],
            'is_profile_public'   => ['boolean'],
            'resume_file'         => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
        ]);

        if ($request->hasFile('resume_file')) {
            $path = $request->file('resume_file')
                ->storeAs("resumes/{$this->profile()->id}", 'resume.pdf', 'local');
            $data['resume_file'] = $path;
        }

        $this->profile()->update($data);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updateResume(Request $request): RedirectResponse
    {
        $request->validate([
            'resume_file' => ['required', 'file', 'mimes:pdf', 'max:5120'],
        ]);

        $path = $request->file('resume_file')
            ->storeAs("resumes/{$this->profile()->id}", 'resume.pdf', 'local');

        $this->profile()->update(['resume_file' => $path]);

        return back()->with('success', 'Resume uploaded successfully.');
    }

    public function updatePhoto(Request $request): RedirectResponse
    {
        $request->validate(['avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048']]);

        if (auth()->user()->avatar) {
            Storage::disk('public')->delete(auth()->user()->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        auth()->user()->update(['avatar' => $path]);

        return back()->with('success', 'Photo updated.');
    }

    public function storeEducation(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'degree'               => ['required', 'string', 'max:100'],
            'field_of_study'       => ['required', 'string', 'max:100'],
            'institution_name'     => ['required', 'string', 'max:200'],
            'board_or_university'  => ['nullable', 'string', 'max:200'],
            'passing_year'         => ['required', 'integer', 'min:1970', 'max:' . date('Y')],
            'result_value'         => ['nullable', 'string', 'max:50'],
            'is_highest_education' => ['boolean'],
        ]);

        if (! empty($data['is_highest_education'])) {
            $this->profile()->educations()->update(['is_highest_education' => false]);
        }

        $this->profile()->educations()->create($data);

        return back()->with('success', 'Education entry added.');
    }

    public function destroyEducation(Education $education): RedirectResponse
    {
        $education->delete();
        return back()->with('success', 'Education entry removed.');
    }

    public function storeExperience(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'company_name'     => ['required', 'string', 'max:150'],
            'job_title'        => ['required', 'string', 'max:100'],
            'employment_type'  => ['nullable', 'string'],
            'location'         => ['nullable', 'string', 'max:150'],
            'start_date'       => ['required', 'date'],
            'end_date'         => ['nullable', 'date'],
            'is_current'       => ['boolean'],
            'responsibilities' => ['nullable', 'string', 'max:3000'],
        ]);

        if (! empty($data['is_current'])) {
            $data['end_date'] = null;
            $this->profile()->workExperiences()->update(['is_current' => false]);
        }

        $this->profile()->workExperiences()->create($data);

        return back()->with('success', 'Work experience added.');
    }

    public function destroyExperience(WorkExperience $experience): RedirectResponse
    {
        $experience->delete();
        return back()->with('success', 'Experience entry removed.');
    }

    public function syncSkills(Request $request): RedirectResponse
    {
        $request->validate([
            'skills'               => ['required', 'array', 'max:30'],
            'skills.*.id'          => ['required', 'exists:skills,id'],
            'skills.*.proficiency' => ['required', 'in:beginner,intermediate,advanced,expert'],
            'skills.*.years_used'  => ['nullable', 'integer', 'min:0', 'max:50'],
        ]);

        $syncData = collect($request->skills)->mapWithKeys(fn($s) => [
            $s['id'] => ['proficiency' => $s['proficiency'], 'years_used' => $s['years_used'] ?? 0],
        ])->all();

        $this->profile()->skills()->sync($syncData);

        return back()->with('success', 'Skills updated.');
    }
}