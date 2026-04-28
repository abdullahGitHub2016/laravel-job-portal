<?php
namespace App\Http\Resources\Job;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                   => $this->id,
            'title'                => $this->title,
            'slug'                 => $this->slug,
            'description'          => $this->description,
            'requirements'         => $this->requirements,
            'benefits'             => $this->benefits,
            'job_type'             => $this->job_type,
            'job_type_label'       => ucwords(str_replace('_', ' ', $this->job_type)),
            'experience_level'     => $this->experience_level,
            'experience_label'     => ucfirst($this->experience_level),
            'vacancies'            => $this->vacancies,
            'education_level'      => $this->education_level,
            'location'             => $this->location,
            'district'             => $this->district,
            'country'              => $this->country,
            'is_remote'            => $this->is_remote,
            'salary_type'          => $this->salary_type,
            'salary_min'           => $this->salary_min,
            'salary_max'           => $this->salary_max,
            'currency'             => $this->currency,
            'show_salary'          => $this->show_salary,
            'salary_display'       => $this->formatSalary(),
            'is_featured'          => $this->is_featured,
            'is_hot'               => $this->is_hot,
            'is_urgent'            => $this->is_urgent,
            'status'               => $this->status,
            'published_at'         => $this->published_at?->diffForHumans(),
            'published_at_raw'     => $this->published_at?->toDateString(),
            'application_deadline' => $this->application_deadline?->format('d M Y'),
            'deadline_raw'         => $this->application_deadline?->toDateString(),
            'days_remaining'       => $this->application_deadline
                                        ? max(0, now()->diffInDays($this->application_deadline, false))
                                        : null,
            'view_count'           => $this->view_count,
            'application_count'    => $this->application_count ?? $this->applications_count ?? 0,
            'category'   => $this->whenLoaded('category', fn() => [
                'id'   => $this->category->id,
                'name' => $this->category->name,
                'slug' => $this->category->slug,
            ]),
            'employer'   => $this->whenLoaded('employerProfile', fn() => [
                'id'          => $this->employerProfile->id,
                'name'        => $this->employerProfile->company_name,
                'logo'        => $this->employerProfile->logo
                                    ? asset('storage/' . $this->employerProfile->logo)
                                    : null,
                'district'    => $this->employerProfile->district,
                'is_verified' => $this->employerProfile->verification_status === 'verified',
                'is_premium'  => $this->employerProfile->is_premium,
            ]),
            'skills' => $this->whenLoaded('skills', fn() =>
                $this->skills->map(fn($s) => [
                    'id'          => $s->id,
                    'name'        => $s->name,
                    'is_required' => (bool) $s->pivot->is_required,
                ])
            ),
        ];
    }

    private function formatSalary(): string
    {
        if (! $this->show_salary || $this->salary_type === 'negotiable') {
            return 'Negotiable';
        }

        $currency = $this->currency === 'BDT' ? '৳' : $this->currency;
        $suffix   = match($this->salary_type) {
            'monthly' => '/mo',
            'yearly'  => '/yr',
            'hourly'  => '/hr',
            default   => '',
        };

        if ($this->salary_min && $this->salary_max) {
            return $currency . number_format($this->salary_min) . ' – ' . $currency . number_format($this->salary_max) . $suffix;
        }

        if ($this->salary_min) {
            return 'From ' . $currency . number_format($this->salary_min) . $suffix;
        }

        return 'Negotiable';
    }
}
