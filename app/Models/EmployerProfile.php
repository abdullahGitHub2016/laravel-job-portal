<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployerProfile extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'user_id','industry_id','company_name','slug','company_overview','company_type',
        'company_size','founded_year','website','logo','cover_image','social_links',
        'address','district','country','verification_status','verified_at','verified_by',
        'is_premium','premium_expires_at','is_active',
    ];

    protected $casts = [
        'social_links'       => 'array',
        'is_premium'         => 'boolean',
        'is_active'          => 'boolean',
        'verified_at'        => 'datetime',
        'premium_expires_at' => 'datetime',
    ];

    public function user(): BelongsTo     { return $this->belongsTo(User::class); }
    public function industry(): BelongsTo { return $this->belongsTo(Industry::class); }
    public function jobPosts(): HasMany   { return $this->hasMany(JobPost::class); }

    public function activeJobPosts(): HasMany
    {
        return $this->hasMany(JobPost::class)
                    ->where('status', 'published')
                    ->where('application_deadline', '>=', now());
    }

    public function scopeVerified($query)
    {
        return $query->where('verification_status', 'verified')->where('is_active', true);
    }
}
