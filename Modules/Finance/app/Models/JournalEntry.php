<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\Concerns\BelongsToTenant;
use Modules\Core\Models\Organization;
use Modules\Core\Models\User;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
use RuntimeException;

class JournalEntry extends Model
{
    use BelongsToTenant, HasFactory, LogsActivity, SoftDeletes;

    private bool $allowReversalMutation = false;

    protected static function booted(): void
    {
        static::updating(function (self $entry): void {
            if (! $entry->getRawOriginal('is_posted')) {
                return;
            }

            if (! $entry->allowReversalMutation
                || array_diff(array_keys($entry->getDirty()), ['is_reversed', 'reversal_reason', 'updated_at']) !== []) {
                throw new RuntimeException('Posted journals are immutable; use a correcting reversal entry.');
            }
        });

        static::deleting(function (self $entry): void {
            if ($entry->isLockedForMutation()) {
                throw new RuntimeException('Posted and reversed journals cannot be deleted.');
            }
        });
    }

    public function recordReversal(string $reason): void
    {
        if (! $this->is_posted || $this->is_reversed) {
            throw new RuntimeException('Only posted and unreversed journal entries can be reversed.');
        }

        $this->allowReversalMutation = true;

        try {
            $this->forceFill([
                'is_reversed' => true,
                'reversal_reason' => $reason,
            ])->save();
        } finally {
            $this->allowReversalMutation = false;
        }
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['entry_number', 'reference', 'total_debit', 'total_credit', 'status', 'posted_by', 'posted_at'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    protected $fillable = [
        'tenant_id',
        'organization_id',
        'posted_by',
        'reversed_entry_id',
        'entry_number',
        'date',
        'description',
        'total_debit',
        'total_credit',
        'is_balanced',
        'is_posted',
        'posted_at',
        'is_reversed',
        'reversal_reason',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'total_debit' => 'decimal:2',
            'total_credit' => 'decimal:2',
            'is_balanced' => 'boolean',
            'is_posted' => 'boolean',
            'posted_at' => 'datetime',
            'is_reversed' => 'boolean',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function postedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function reversedEntry(): BelongsTo
    {
        return $this->belongsTo(self::class, 'reversed_entry_id');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    public function isLockedForMutation(): bool
    {
        return (bool) $this->is_posted || (bool) $this->is_reversed;
    }
}
