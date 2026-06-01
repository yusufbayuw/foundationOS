<?php

namespace Modules\Monitoring\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Modules\Core\Models\Concerns\BelongsToTenant;

class AutomationRun extends Model
{
    use BelongsToTenant;

    public const STATUS_PENDING = 'pending';

    public const STATUS_RUNNING = 'running';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_FAILED = 'failed';

    public const STATUS_COMPENSATED = 'compensated';

    protected $fillable = [
        'tenant_id',
        'parent_id',
        'trigger_event',
        'subject_type',
        'subject_id',
        'action_type',
        'status',
        'payload',
        'result',
        'error_message',
        'started_at',
        'finished_at',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'result' => 'array',
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function markRunning(): void
    {
        $this->forceFill([
            'status' => self::STATUS_RUNNING,
            'started_at' => now(),
        ])->save();
    }

    public function markCompleted(mixed $result = null): void
    {
        $this->forceFill([
            'status' => self::STATUS_COMPLETED,
            'result' => is_array($result) ? $result : ['value' => $result],
            'finished_at' => now(),
        ])->save();
    }

    public function markFailed(\Throwable $exception): void
    {
        $this->forceFill([
            'status' => self::STATUS_FAILED,
            'error_message' => $exception->getMessage(),
            'finished_at' => now(),
        ])->save();
    }
}
