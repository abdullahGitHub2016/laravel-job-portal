<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Application extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'job_post_id','job_seeker_profile_id','cover_letter','resume_snapshot',
        'expected_salary','status','employer_notes','reviewed_by','reviewed_at',
        'is_seen_by_seeker',
    ];

    protected $casts = [
        'reviewed_at'       => 'datetime',
        'is_seen_by_seeker' => 'boolean',
    ];

    public function jobPost(): BelongsTo            { return $this->belongsTo(JobPost::class); }
    public function jobSeekerProfile(): BelongsTo   { return $this->belongsTo(JobSeekerProfile::class); }
    public function reviewedBy(): BelongsTo         { return $this->belongsTo(User::class, 'reviewed_by'); }
}
