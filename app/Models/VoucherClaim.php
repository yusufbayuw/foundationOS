<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\Concerns\BelongsToTenant;

class VoucherClaim extends Model
{
    use BelongsToTenant;

    protected $fillable = ['tenant_id', 'voucher_id', 'user_id', 'status', 'redeemed_at'];

    protected function casts(): array
    {
        return ['redeemed_at' => 'datetime'];
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
