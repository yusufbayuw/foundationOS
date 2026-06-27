<?php

namespace Modules\Sales\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\Concerns\BelongsToTenant;
use Modules\Sales\Database\Factories\CustomerFactory;

class Customer extends Model
{
    /** @use HasFactory<CustomerFactory> */
    use BelongsToTenant, HasFactory, SoftDeletes;

    protected static function newFactory(): CustomerFactory
    {
        return CustomerFactory::new();
    }

    protected $fillable = [
        'tenant_id',
        'organization_id',
        'code',
        'name',
        'email',
        'phone',
        'address',
        'is_active',
        'is_cooperative_member',
        'member_number',
        'member_discount_percent',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_cooperative_member' => 'boolean',
            'member_discount_percent' => 'decimal:2',
        ];
    }

    /**
     * @return HasMany<SalesOrder, $this>
     */
    public function salesOrders(): HasMany
    {
        return $this->hasMany(SalesOrder::class);
    }
}
