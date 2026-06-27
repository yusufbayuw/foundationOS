<?php

namespace Modules\Procurement\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\Concerns\BelongsToTenant;
use Modules\Finance\Models\ChartOfAccount;

class ProcurementItem extends Model
{
    /** @use HasFactory<Factory<static>> */
    use BelongsToTenant, HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'category_id',
        'preferred_vendor_id',
        'chart_of_account_id',
        'code',
        'name',
        'description',
        'unit_of_measure',
        'estimated_price',
        'last_purchase_price',
        'specifications',
        'minimum_order_quantity',
        'lead_time_days',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'estimated_price' => 'decimal:2',
            'last_purchase_price' => 'decimal:2',
            'minimum_order_quantity' => 'integer',
            'lead_time_days' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<ProcurementCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ProcurementCategory::class, 'category_id');
    }

    /**
     * @return BelongsTo<Vendor, $this>
     */
    public function preferredVendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'preferred_vendor_id');
    }

    /**
     * @return BelongsTo<ChartOfAccount, $this>
     */
    public function chartOfAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class);
    }

    /**
     * @return HasMany<PurchaseRequisitionItem, $this>
     */
    public function requisitionItems(): HasMany
    {
        return $this->hasMany(PurchaseRequisitionItem::class);
    }

    /**
     * @return HasMany<RfqItem, $this>
     */
    public function rfqItems(): HasMany
    {
        return $this->hasMany(RfqItem::class);
    }

    /**
     * @return HasMany<PurchaseOrderItem, $this>
     */
    public function purchaseOrderItems(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }
}
