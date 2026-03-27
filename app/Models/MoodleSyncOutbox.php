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
}

