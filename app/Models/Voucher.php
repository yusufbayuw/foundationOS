<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\Concerns\BelongsToTenant;

class Voucher extends Model
{
    use BelongsToTenant, SoftDeletes;

    protected $fillable = ['tenant_id', 'organization_id', 'code', 'name', 'status', 'description', 'meta'];

    protected function casts(): array
    {
        return ['meta' => 'array'];
    }

    public function claims(): HasMany
    {
        return $this->hasMany(VoucherClaim::class);
    }
}
