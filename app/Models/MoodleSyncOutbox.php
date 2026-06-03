<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class MoodleSyncOutbox extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_PROCESSING = 'processing';

    public const STATUS_SYNCED = 'synced';

    public const STATUS_FAILED = 'failed';

    public const STATUS_SKIPPED = 'skipped';

    protected $table = 'moodle_sync_outbox';

    protected $fillable = [
        'entity_type',
        'entity_id',
        'tenant_id',
        'action',
        'payload',
        'dedupe_key',
        'status',
        'attempts',
        'next_retry_at',
        'last_error',
        'synced_at',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'attempts' => 'integer',
            'next_retry_at' => 'datetime',
            'synced_at' => 'datetime',
        ];
    }

    public function scopeReady(Builder $query): Builder
    {
        return $query
            ->where('status', self::STATUS_PENDING)
            ->where(function (Builder $inner): void {
                $inner->whereNull('next_retry_at')
                    ->orWhere('next_retry_at', '<=', now());
            });
    }

    /**
     * Atomically claim a row for processing (prevents duplicate workers).
     */
    public static function tryClaim(int $id): ?self
    {
        $updated = static::query()
            ->whereKey($id)
            ->whereIn('status', [self::STATUS_PENDING, self::STATUS_FAILED])
            ->update([
                'status' => self::STATUS_PROCESSING,
                'last_error' => null,
            ]);

        if ($updated !== 1) {
            return null;
        }

        return static::query()->find($id);
    }

    public function scopeStaleProcessing(Builder $query, int $minutes): Builder
    {
        return $query
            ->where('status', self::STATUS_PROCESSING)
            ->where('updated_at', '<=', now()->subMinutes($minutes));
    }
}
