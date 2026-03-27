<?php

namespace Modules\Procurement\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Monitoring\Models\AuditLog;
use Modules\Monitoring\Models\FileUpload;
use Modules\Core\Models\Concerns\BelongsToTenant;

class PurchaseOrder extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'request_for_quotation_id',
        'vendor_id',
        'approved_by',
        'po_number',
        'po_date',
        'delivery_date',
        'delivery_location',
        'payment_terms',
        'subtotal',
        'discount_amount',
        'tax_percentage',
        'tax_amount',
        'shipping_cost',
        'other_costs',
        'total_amount',
        'currency',
        'exchange_rate',
        'status',
        'sent_at',
        'notes',
        'terms_conditions',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'po_date' => 'date',
            'delivery_date' => 'date',
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'tax_percentage' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'shipping_cost' => 'decimal:2',
            'other_costs' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'exchange_rate' => 'decimal:4',
            'sent_at' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }

    public function tenant(): BelongsTo { return $this->belongsTo(Tenant::class); }
    public function requestForQuotation(): BelongsTo { return $this->belongsTo(RequestForQuotation::class); }
    public function vendor(): BelongsTo { return $this->belongsTo(Vendor::class); }
    public function approver(): BelongsTo { return $this->belongsTo(User::class, 'approved_by'); }
    public function items(): HasMany { return $this->hasMany(PurchaseOrderItem::class); }
    public function goodsReceipts(): HasMany { return $this->hasMany(GoodsReceipt::class); }
    public function vendorBills(): HasMany { return $this->hasMany(VendorBill::class); }
    public function requisitionItems(): HasMany { return $this->hasMany(PurchaseRequisitionItem::class); }

    public function auditLogs(): MorphMany
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }

    public function fileUploads(): MorphMany
    {
        return $this->morphMany(FileUpload::class, 'fileable');
    }
}
