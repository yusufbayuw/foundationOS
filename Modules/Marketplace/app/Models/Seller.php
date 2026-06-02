<?php

namespace Modules\Marketplace\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\Concerns\BelongsToTenant;
use Modules\Marketplace\Database\Factories\SellerFactory;

class Seller extends Model
{
    /** @use HasFactory<SellerFactory> */
    use BelongsToTenant, HasFactory, SoftDeletes;

    protected static function newFactory(): SellerFactory
    {
        return SellerFactory::new();
    }

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
