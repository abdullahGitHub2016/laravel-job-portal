<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobSeekerProfile extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'user_id','headline','bio','current_job_title','current_company',
        'years_of_experience','location','district','country','gender',
        'date_of_birth','nationality','expected_salary_min','expected_salary_max',
        'preferred_job_type','preferred_locations','job_seeking_status',
        'is_profile_public','resume_file','resume_data',
    ];

    protected $casts = [
        'resume_data'         => 'array',
        'preferred_locations' => 'array',
        'is_profile_public'   => 'boolean',
        'date_of_birth'       => 'date',
    ];

    public function user(): BelongsTo           { return $this->belongsTo(User::class); }
    public function educations(): HasMany        { return $this->hasMany(Education::class)->orderByDesc('passing_year'); }
    public function workExperiences(): HasMany   { return $this->hasMany(WorkExperience::class)->orderByDesc('start_date'); }
    public function applications(): HasMany      { return $this->hasMany(Application::class); }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'job_seeker_skills')
                    ->withPivot('proficiency', 'years_used');
    }

    public function savedJobs(): BelongsToMany
    {
        return $this->belongsToMany(JobPost::class, 'saved_jobs')
                    ->withPivot('note')->withTimestamps();
    }

    public function scopeActivelyLooking($query)
    {
        return $query->where('job_seeking_status', 'actively_looking')
                     ->where('is_profile_public', true);
    }
}
