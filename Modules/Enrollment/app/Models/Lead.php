<?php

namespace Modules\Enrollment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\Concerns\BelongsToTenant;
use Modules\Core\Models\User;

class Lead extends Model
{
    use BelongsToTenant, SoftDeletes;

    public const STAGES = ['new', 'contacted', 'interested', 'applied', 'enrolled', 'lost'];

    protected $fillable = [
        'tenant_id',
        'lead_source_id',
        'marketing_campaign_id',
        'assigned_to_user_id',
        'full_name',
        'email',
        'phone',
        'stage',
        'source_detail',
        'utm',
        'next_follow_up_at',
        'converted_at',
        'applicant_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'utm' => 'array',
            'next_follow_up_at' => 'datetime',
            'converted_at' => 'datetime',
        ];
    }

    public function leadSource(): BelongsTo
    {
        return $this->belongsTo(LeadSource::class);
    }

    public function marketingCampaign(): BelongsTo
    {
        return $this->belongsTo(MarketingCampaign::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(LeadActivity::class);
    }
}
