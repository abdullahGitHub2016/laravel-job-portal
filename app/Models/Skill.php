<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Skill extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['name', 'slug', 'category', 'usage_count'];

    public function jobSeekers(): BelongsToMany
    {
        return $this->belongsToMany(JobSeekerProfile::class, 'job_seeker_skills')
                    ->withPivot('proficiency', 'years_used');
    }

    public function jobPosts(): BelongsToMany
    {
        return $this->belongsToMany(JobPost::class, 'job_post_skills')
                    ->withPivot('is_required');
    }
}
