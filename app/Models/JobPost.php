<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobPost extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'employer_profile_id',
        'category_id',
        'title',
        'slug',
        'description',
        'requirements',
        'job_type',
        'experience_level',
        'gender_preference',
        'salary_type',
        'salary_min',
        'salary_max',
        'currency',
        'show_salary',
        'is_remote',
        'location',
        'district',
        'country',
        'experience_years_min',
        'experience_years_max',
        'education_level',
        'vacancies',
        'status',
        'is_featured',
        'is_hot',
        'is_urgent',
        'published_at',
        'application_deadline',
        'featured_until',
        'view_count',
        'application_count',
    ];

    protected $casts = [
        'is_featured'          => 'boolean',
        'is_hot'               => 'boolean',
        'is_urgent'            => 'boolean',
        'is_remote'            => 'boolean',
        'show_salary'          => 'boolean',
        'published_at'         => 'datetime',
        'featured_until'       => 'datetime',
        'application_deadline' => 'date',
    ];

    public function employerProfile(): BelongsTo
    {
        return $this->belongsTo(EmployerProfile::class);
    }
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'job_post_skills')->withPivot('is_required');
    }

    // ✅ Benefits relation
    public function benefits(): BelongsToMany
    {
        return $this->belongsToMany(Benefit::class, 'job_post_benefits');
    }

    public function savedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(JobSeekerProfile::class, 'saved_jobs')
            ->withPivot('note')->withTimestamps();
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where('application_deadline', '>=', now());
    }

    public function scopeSearch($query, string $term)
    {
        return $query->whereRaw(
            "to_tsvector('english', coalesce(title,'') || ' ' || coalesce(description,'') || ' ' || coalesce(requirements,'')) @@ plainto_tsquery('english', ?)",
            [$term]
        );
    }
}
