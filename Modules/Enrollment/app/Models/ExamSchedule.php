<?php

namespace Modules\Enrollment\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\Concerns\BelongsToTenant;

class ExamSchedule extends Model
{
    /** @use HasFactory<Factory<static>> */
    use BelongsToTenant, HasFactory, SoftDeletes;

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

    /**
     * @return BelongsTo<AdmissionPeriod, $this>
     */
    public function admissionPeriod(): BelongsTo
    {
        return $this->belongsTo(AdmissionPeriod::class);
    }

    /**
     * @return HasMany<ExamResult, $this>
     */
    public function examResults(): HasMany
    {
        return $this->hasMany(ExamResult::class);
    }

    public function isPrintable(): bool
    {
        return (bool) $this->is_active && $this->date !== null;
    }
}
