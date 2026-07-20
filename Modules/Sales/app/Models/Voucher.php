<?php

namespace Modules\Sales\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\Concerns\BelongsToTenant;
use Modules\Sales\Database\Factories\VoucherFactory;

class Voucher extends Model
{
    /** @use HasFactory<VoucherFactory> */
    use BelongsToTenant, HasFactory, SoftDeletes;

    protected static function newFactory(): VoucherFactory
    {
        return VoucherFactory::new();
    }

    protected $fillable = [
        'tenant_id',
        'organization_id',
        'title',
        'code',
        'status',
    ];

    public function claims(): HasMany
    {
        return $this->hasMany(VoucherClaim::class);
    }
}
