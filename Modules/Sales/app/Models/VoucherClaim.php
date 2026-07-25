<?php

namespace Modules\Sales\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\Concerns\BelongsToTenant;
use Modules\Core\Models\User;
use Modules\Sales\Database\Factories\VoucherClaimFactory;

class VoucherClaim extends Model
{
    /** @use HasFactory<VoucherClaimFactory> */
    use BelongsToTenant, HasFactory;

    protected static function newFactory(): VoucherClaimFactory
    {
        return VoucherClaimFactory::new();
    }

    protected $fillable = [
        'tenant_id',
        'organization_id',
        'voucher_id',
        'user_id',
        'claim_code',
        'status',
        'claimed_at',
        'used_at',
    ];

    protected function casts(): array
    {
        return [
            'claimed_at' => 'datetime',
            'used_at' => 'datetime',
        ];
    }

    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
