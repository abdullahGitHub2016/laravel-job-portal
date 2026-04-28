<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkExperience extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'job_seeker_profile_id','company_name','job_title','employment_type',
        'location','start_date','end_date','is_current','responsibilities',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'is_current' => 'boolean',
    ];

    public function jobSeekerProfile(): BelongsTo { return $this->belongsTo(JobSeekerProfile::class); }
}
