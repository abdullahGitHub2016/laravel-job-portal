<?php
namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Http\Resources\Job\JobResource;
use App\Models\Category;
use App\Models\JobPost;
use App\Models\Skill;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class JobController extends Controller
{
    private function employerProfile()
    {
        return auth()->user()->employerProfile;
    }

    public function index(): Response
    {
        $jobs = $this->employerProfile()
            ->jobPosts()
            ->withCount('applications')
            ->orderByDesc('created_at')
            ->paginate(10);

        return Inertia::render('Employer/Jobs/Index', [
            'jobs' => JobResource::collection($jobs),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Employer/Jobs/Create', [
            'categories' => Category::where('is_active', true)->get(['id', 'name']),
            'skills'     => Skill::orderBy('name')->get(['id', 'name', 'category']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title'                => ['required', 'string', 'max:150'],
            'description'          => ['required', 'string', 'min:50'],
            'requirements'         => ['nullable', 'string'],
            'benefits'             => ['nullable', 'string'],
            'category_id'          => ['required', 'exists:categories,id'],
            'job_type'             => ['required', 'in:full_time,part_time,contract,internship,freelance,remote,hybrid'],
            'experience_level'     => ['required', 'in:entry,junior,mid,senior,lead,executive'],
            'experience_years_min' => ['required', 'integer', 'min:0'],
            'vacancies'            => ['required', 'integer', 'min:1'],
            'salary_type'          => ['required', 'in:monthly,yearly,hourly,negotiable'],
            'salary_min'           => ['nullable', 'numeric', 'min:0'],
            'salary_max'           => ['nullable', 'numeric', 'min:0'],
            'currency'             => ['required', 'string', 'size:3'],
            'show_salary'          => ['boolean'],
            'is_remote'            => ['boolean'],
            'location'             => ['nullable', 'string', 'max:200'],
            'district'             => ['nullable', 'string', 'max:100'],
            'application_deadline' => ['required', 'date', 'after:today'],
            'skills'               => ['nullable', 'array'],
            'skills.*.id'          => ['required', 'exists:skills,id'],
            'skills.*.required'    => ['boolean'],
        ]);

        $data['slug']                = Str::slug($data['title']) . '-' . Str::random(6);
        $data['employer_profile_id'] = $this->employerProfile()->id;
        $data['status']              = 'published';
        $data['published_at']        = now();

        $skillsData = $data['skills'] ?? [];
        unset($data['skills']);

        $job = JobPost::create($data);

        if (! empty($skillsData)) {
            $pivot = collect($skillsData)->mapWithKeys(
                fn($s) => [$s['id'] => ['is_required' => $s['required'] ?? true]]
            )->all();
            $job->skills()->sync($pivot);
        }

        return redirect()->route('employer.jobs.index')->with('success', 'Job post published!');
    }

    public function edit(JobPost $job): Response
    {
        $job->load('skills', 'category');

        return Inertia::render('Employer/Jobs/Create', [
            'job'        => new JobResource($job),
            'categories' => Category::where('is_active', true)->get(['id', 'name']),
            'skills'     => Skill::orderBy('name')->get(['id', 'name', 'category']),
        ]);
    }

    public function update(Request $request, JobPost $job): RedirectResponse
    {
        $data = $request->validate([
            'title'                => ['required', 'string', 'max:150'],
            'description'          => ['required', 'string', 'min:50'],
            'requirements'         => ['nullable', 'string'],
            'benefits'             => ['nullable', 'string'],
            'category_id'          => ['required', 'exists:categories,id'],
            'job_type'             => ['required', 'in:full_time,part_time,contract,internship,freelance,remote,hybrid'],
            'experience_level'     => ['required', 'in:entry,junior,mid,senior,lead,executive'],
            'experience_years_min' => ['required', 'integer', 'min:0'],
            'vacancies'            => ['required', 'integer', 'min:1'],
            'salary_type'          => ['required', 'in:monthly,yearly,hourly,negotiable'],
            'salary_min'           => ['nullable', 'numeric', 'min:0'],
            'salary_max'           => ['nullable', 'numeric', 'min:0'],
            'currency'             => ['required', 'string', 'size:3'],
            'show_salary'          => ['boolean'],
            'is_remote'            => ['boolean'],
            'location'             => ['nullable', 'string', 'max:200'],
            'district'             => ['nullable', 'string', 'max:100'],
            'application_deadline' => ['required', 'date'],
            'skills'               => ['nullable', 'array'],
        ]);

        $skillsData = $data['skills'] ?? [];
        unset($data['skills']);
        $job->update($data);

        if (! empty($skillsData)) {
            $pivot = collect($skillsData)->mapWithKeys(
                fn($s) => [$s['id'] => ['is_required' => $s['required'] ?? true]]
            )->all();
            $job->skills()->sync($pivot);
        }

        return back()->with('success', 'Job updated successfully.');
    }

    public function destroy(JobPost $job): RedirectResponse
    {
        $job->delete();
        return redirect()->route('employer.jobs.index')->with('success', 'Job deleted.');
    }

    public function updateStatus(Request $request, JobPost $job): RedirectResponse
    {
        $request->validate(['status' => 'required|in:draft,published,archived']);

        $job->update([
            'status'       => $request->status,
            'published_at' => $request->status === 'published' && ! $job->published_at ? now() : $job->published_at,
        ]);

        return back()->with('success', 'Status updated to: ' . ucfirst($request->status));
    }
}
