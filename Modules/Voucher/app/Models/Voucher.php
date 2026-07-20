<?php

namespace Modules\Voucher\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Core\Models\Concerns\BelongsToTenant;

class Voucher extends Model
{
    use BelongsToTenant;

    protected $fillable = ['tenant_id', 'code', 'name', 'status'];

    public function claims(): HasMany
    {
        return $this->hasMany(VoucherClaim::class);
    }
}
