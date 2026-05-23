<?php

namespace Modules\Marketplace\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Modules\Core\Models\Concerns\BelongsToTenant;

class Seller extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'organization_id',
        'code',
        'name',
        'status',
        'description',
        'meta',
        'seller_type',
        'seller_id',
        'verification_status',
    ];

    protected function casts(): array
    {
        return ['meta' => 'array'];
    }

    public function sellable(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'seller_type', 'seller_id');
    }
}
