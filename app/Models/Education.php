<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Education extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'job_seeker_profile_id','degree','field_of_study','institution_name',
        'board_or_university','passing_year','result','result_value','is_highest_education',
    ];

    protected $casts = ['is_highest_education' => 'boolean'];

    public function jobSeekerProfile(): BelongsTo { return $this->belongsTo(JobSeekerProfile::class); }
}
