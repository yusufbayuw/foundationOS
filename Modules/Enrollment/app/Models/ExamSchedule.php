<?php

namespace Modules\Enrollment\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Core\Models\Tenant;

class ExamSchedule extends Model
{
    use HasFactory, SoftDeletes;

    public function synchronizeCounters(): void
    {
        $this->forceFill([
            'registered_count' => $this->examResults()->count(),
        ])->saveQuietly();
    }

    protected $fillable = [
        'tenant_id',
        'admission_period_id',
        'name',
        'type',
        'date',
        'start_time',
        'end_time',
        'location',
        'room_capacity',
        'registered_count',
        'instructions',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'room_capacity' => 'integer',
            'registered_count' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function admissionPeriod(): BelongsTo
    {
        return $this->belongsTo(AdmissionPeriod::class);
    }

    public function examResults(): HasMany
    {
        return $this->hasMany(ExamResult::class);
    }
}
