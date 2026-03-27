<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Core\Models\Organization;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;

class JournalEntry extends Model
{
    use HasFactory, SoftDeletes;

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

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
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
}
