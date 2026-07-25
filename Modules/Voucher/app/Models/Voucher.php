<?php

namespace Modules\Voucher\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\Concerns\BelongsToTenant;
use Modules\Voucher\Database\Factories\VoucherFactory;

class Voucher extends Model
{
    /** @use HasFactory<VoucherFactory> */
    use BelongsToTenant, HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'owner_type',
        'owner_id',
        'title',
        'description',
        'code',
        'quota',
        'claimed_count',
        'start_at',
        'end_at',
        'status',
        'terms',
        'redemption_method',
    ];

    protected function casts(): array
    {
        return [
            'quota' => 'integer',
            'claimed_count' => 'integer',
            'start_at' => 'datetime',
            'end_at' => 'datetime',
        ];
    }

    protected static function newFactory(): VoucherFactory
    {
        return VoucherFactory::new();
    }

    public function owner(): MorphTo
    {
        return $this->morphTo();
    }

    public function claims(): HasMany
    {
        return $this->hasMany(VoucherClaim::class);
    }

    public function isClaimable(): bool
    {
        $now = now();

        return $this->status === 'active'
            && ($this->start_at === null || $this->start_at->lte($now))
            && ($this->end_at === null || $this->end_at->gte($now))
            && $this->claimed_count < $this->quota;
    }
}
