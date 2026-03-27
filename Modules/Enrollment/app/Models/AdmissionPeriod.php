<?php

namespace Modules\Enrollment\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Modules\Core\Models\Organization;
use Modules\Core\Models\Tenant;

class AdmissionPeriod extends Model
{
    use HasFactory, SoftDeletes;

    public function synchronizeCounters(): void
    {
        $this->forceFill([
            'registered_count' => $this->applicants()->count(),
            'accepted_count' => $this->applicants()
                ->where(function ($query) {
                    $query->whereNotNull('accepted_program_id')
                        ->orWhere('is_passed', true);
                })
                ->count(),
        ])->saveQuietly();
    }

    protected $fillable = [
        'tenant_id',
        'organization_id',
        'name',
        'code',
        'type',
        'start_date',
        'end_date',
        'announcement_date',
        'registration_fee',
        'quota',
        'registered_count',
        'accepted_count',
        'is_active',
        'description',
        'requirements',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'announcement_date' => 'date',
            'registration_fee' => 'decimal:2',
            'quota' => 'integer',
            'registered_count' => 'integer',
            'accepted_count' => 'integer',
            'is_active' => 'boolean',
            'requirements' => 'array',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function applicants(): HasMany
    {
        return $this->hasMany(Applicant::class);
    }

    public function examSchedules(): HasMany
    {
        return $this->hasMany(ExamSchedule::class);
    }

    public function registrations(): HasManyThrough
    {
        return $this->hasManyThrough(Registration::class, Applicant::class);
    }
}
