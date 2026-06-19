<?php

namespace Modules\Procurement\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\Concerns\BelongsToTenant;
use Modules\Finance\Models\ChartOfAccount;

class ProcurementItem extends Model
{
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

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProcurementCategory::class, 'category_id');
    }

    public function preferredVendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'preferred_vendor_id');
    }

    public function chartOfAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class);
    }

    public function requisitionItems(): HasMany
    {
        return $this->hasMany(PurchaseRequisitionItem::class);
    }

    public function rfqItems(): HasMany
    {
        return $this->hasMany(RfqItem::class);
    }

    public function purchaseOrderItems(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }
}
