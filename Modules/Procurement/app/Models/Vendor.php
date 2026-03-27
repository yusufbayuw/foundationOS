<?php

namespace Modules\Procurement\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Modules\Monitoring\Models\AuditLog;
use Modules\Monitoring\Models\FileUpload;
use Modules\Global\Models\City;
use Modules\Global\Models\Province;
use Modules\Core\Models\Concerns\BelongsToTenant;

class Vendor extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'province_id',
        'city_id',
        'code',
        'name',
        'type',
        'business_field',
        'npwp',
        'nib',
        'siup',
        'tdp',
        'address',
        'postal_code',
        'phone',
        'email',
        'website',
        'contact_person',
        'contact_position',
        'contact_phone',
        'contact_email',
        'bank_name',
        'bank_account',
        'bank_account_holder',
        'tax_status',
        'is_active',
        'is_blacklisted',
        'blacklist_reason',
        'performance_rating',
        'total_transactions',
        'total_transaction_value',
        'documents',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_blacklisted' => 'boolean',
            'performance_rating' => 'decimal:2',
            'total_transactions' => 'integer',
            'total_transaction_value' => 'decimal:2',
            'documents' => 'array',
        ];
    }
    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function preferredProcurementItems(): HasMany
    {
        return $this->hasMany(ProcurementItem::class, 'preferred_vendor_id');
    }

    public function preferredRequisitionItems(): HasMany
    {
        return $this->hasMany(PurchaseRequisitionItem::class, 'preferred_vendor_id');
    }

    public function rfqVendors(): HasMany
    {
        return $this->hasMany(RfqVendor::class);
    }

    public function vendorBills(): HasMany
    {
        return $this->hasMany(VendorBill::class);
    }

    public function auditLogs(): MorphMany
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }

    public function fileUploads(): MorphMany
    {
        return $this->morphMany(FileUpload::class, 'fileable');
    }
}
