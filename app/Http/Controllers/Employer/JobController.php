<?php
namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Http\Resources\Job\JobResource;
use App\Models\Benefit;
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

    private function formData(): array
    {
        return [
            'categories'        => Category::where('is_active', true)
                                    ->orderBy('name')
                                    ->get(['id', 'name']),
            'skills'            => Skill::orderBy('name')
                                    ->get(['id', 'name', 'category']),
            'availableBenefits' => Benefit::where('is_active', true)
                                    ->orderBy('category')
                                    ->orderBy('sort_order')
                                    ->get(['id', 'name', 'icon', 'category']),
        ];
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
        return Inertia::render('Employer/Jobs/Create', $this->formData());
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title'                => ['required', 'string', 'max:150'],
            'description'          => ['required', 'string', 'min:50'],
            'requirements'         => ['nullable', 'string'],
            'category_id'          => ['required', 'exists:categories,id'],
            'job_type'             => ['required', 'in:full_time,part_time,contract,internship,freelance,remote,hybrid'],
            'experience_level'     => ['required', 'in:entry,junior,mid,senior,lead,executive'],
            'experience_years_min' => ['required', 'integer', 'min:0'],
            'vacancies'            => ['required', 'integer', 'min:1'],
            'salary_type'          => ['required', 'in:monthly,yearly,hourly,negotiable'],
            'salary_min'           => ['nullable', 'numeric', 'min:0'],
            'salary_max'           => ['nullable', 'numeric', 'min:0'],
            'currency'             => ['nullable', 'string'],
            'show_salary'          => ['nullable', 'boolean'],
            'is_remote'            => ['nullable', 'boolean'],
            'location'             => ['nullable', 'string', 'max:200'],
            'district'             => ['nullable', 'string', 'max:100'],
            'gender_preference'    => ['nullable', 'in:any,male,female'],
            'application_deadline' => ['required', 'date', 'after:today'],
            'status'               => ['required', 'in:draft,published'],
            'skills'               => ['nullable', 'array'],
            'skills.*.id'          => ['required', 'exists:skills,id'],
            'skills.*.required'    => ['nullable', 'boolean'],
            'benefit_ids'          => ['nullable', 'array'],
            'benefit_ids.*'        => ['exists:benefits,id'],
        ]);

        $data['currency']          = $data['currency'] ?? 'BDT';
        $data['show_salary']       = $data['show_salary'] ?? true;
        $data['is_remote']         = $data['is_remote'] ?? false;
        $data['gender_preference'] = $data['gender_preference'] ?? 'any';

        $skillsData = $data['skills'] ?? [];
        $benefitIds = $data['benefit_ids'] ?? [];
        unset($data['skills'], $data['benefit_ids']);

        $data['slug']                = Str::slug($data['title']) . '-' . Str::random(6);
        $data['employer_profile_id'] = $this->employerProfile()->id;

        if ($data['status'] === 'published') {
            $data['published_at'] = now();
        }

        $job = JobPost::create($data);

        if (!empty($skillsData)) {
            $pivot = collect($skillsData)->mapWithKeys(
                fn($s) => [$s['id'] => ['is_required' => $s['required'] ?? true]]
            )->all();
            $job->skills()->sync($pivot);
        }

        $job->benefits()->sync($benefitIds);

        $message = $data['status'] === 'published'
            ? 'Job published successfully!'
            : 'Job saved as draft.';

        return redirect()->route('employer.jobs.index')->with('success', $message);
    }

    public function edit(JobPost $job): Response
    {
        // Fresh load — bypass any cached empty collections from route model binding
        $job->load(['skills', 'category', 'benefits']);

        $resolved = (new JobResource($job))->resolve();

        // Force override directly from DB-loaded collections
        $resolved['benefits'] = $job->getRelation('benefits')->map(fn($b) => [
            'id'       => (string) $b->id,
            'name'     => $b->name,
            'icon'     => $b->icon,
            'category' => $b->category,
        ])->values()->all();

        $resolved['skills'] = $job->getRelation('skills')->map(fn($s) => [
            'id'          => (string) $s->id,
            'name'        => $s->name,
            'is_required' => (bool) $s->pivot->is_required,
        ])->values()->all();

        return Inertia::render('Employer/Jobs/Create', array_merge(
            $this->formData(),
            ['job' => $resolved]
        ));
    }

    public function update(Request $request, JobPost $job): RedirectResponse
    {
        $data = $request->validate([
            'title'                => ['required', 'string', 'max:150'],
            'description'          => ['required', 'string', 'min:50'],
            'requirements'         => ['nullable', 'string'],
            'category_id'          => ['required', 'exists:categories,id'],
            'job_type'             => ['required', 'in:full_time,part_time,contract,internship,freelance,remote,hybrid'],
            'experience_level'     => ['required', 'in:entry,junior,mid,senior,lead,executive'],
            'experience_years_min' => ['required', 'integer', 'min:0'],
            'vacancies'            => ['required', 'integer', 'min:1'],
            'salary_type'          => ['required', 'in:monthly,yearly,hourly,negotiable'],
            'salary_min'           => ['nullable', 'numeric', 'min:0'],
            'salary_max'           => ['nullable', 'numeric', 'min:0'],
            'currency'             => ['nullable', 'string'],
            'show_salary'          => ['nullable', 'boolean'],
            'is_remote'            => ['nullable', 'boolean'],
            'location'             => ['nullable', 'string', 'max:200'],
            'district'             => ['nullable', 'string', 'max:100'],
            'gender_preference'    => ['nullable', 'in:any,male,female'],
            'application_deadline' => ['required', 'date'],
            'status'               => ['required', 'in:draft,published'],
            'skills'               => ['nullable', 'array'],
            'skills.*.id'          => ['required', 'exists:skills,id'],
            'skills.*.required'    => ['nullable', 'boolean'],
            'benefit_ids'          => ['nullable', 'array'],
            'benefit_ids.*'        => ['exists:benefits,id'],
        ]);

        $data['currency']          = $data['currency'] ?? 'BDT';
        $data['show_salary']       = $data['show_salary'] ?? true;
        $data['is_remote']         = $data['is_remote'] ?? false;
        $data['gender_preference'] = $data['gender_preference'] ?? 'any';

        $skillsData = $data['skills'] ?? [];
        $benefitIds = $data['benefit_ids'] ?? [];
        unset($data['skills'], $data['benefit_ids']);

        if ($data['status'] === 'published' && !$job->published_at) {
            $data['published_at'] = now();
        }

        $job->update($data);

        if (!empty($skillsData)) {
            $pivot = collect($skillsData)->mapWithKeys(
                fn($s) => [$s['id'] => ['is_required' => $s['required'] ?? true]]
            )->all();
            $job->skills()->sync($pivot);
        }

        $job->benefits()->sync($benefitIds);

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
            'published_at' => $request->status === 'published' && !$job->published_at
                ? now()
                : $job->published_at,
        ]);
        return back()->with('success', 'Status updated to: ' . ucfirst($request->status));
    }
}