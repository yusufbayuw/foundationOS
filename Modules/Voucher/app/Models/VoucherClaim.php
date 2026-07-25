<?php

namespace Modules\Voucher\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\Concerns\BelongsToTenant;
use Modules\Core\Models\User;

class VoucherClaim extends Model
{
    use BelongsToTenant;

    protected $fillable = ['tenant_id', 'voucher_id', 'user_id', 'claim_code', 'status', 'claimed_at', 'used_at'];

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
