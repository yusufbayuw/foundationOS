<?php

namespace Modules\Procurement\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\Concerns\BelongsToTenant;
use Modules\Core\Models\Department;
use Modules\Core\Models\Tenant;
use Modules\Finance\Models\ChartOfAccount;

class PurchaseRequisitionItem extends Model
{
    use BelongsToTenant, HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'purchase_requisition_id',
        'procurement_item_id',
        'preferred_vendor_id',
        'department_id',
        'purchase_order_id',
        'description',
        'specifications',
        'quantity_requested',
        'unit_of_measure',
        'estimated_unit_price',
        'estimated_total_price',
        'required_date',
        'usage_purpose',
        'budget_account_id',
        'status',
        'ordered_quantity',
        'received_quantity',
        'rejection_reason',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity_requested' => 'integer',
            'estimated_unit_price' => 'decimal:2',
            'estimated_total_price' => 'decimal:2',
            'required_date' => 'date',
            'ordered_quantity' => 'integer',
            'received_quantity' => 'integer',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function requisition(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequisition::class, 'purchase_requisition_id');
    }

    public function procurementItem(): BelongsTo
    {
        return $this->belongsTo(ProcurementItem::class);
    }

    public function preferredVendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'preferred_vendor_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function budgetAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'budget_account_id');
    }
}
