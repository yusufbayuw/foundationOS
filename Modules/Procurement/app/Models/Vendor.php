<?php

namespace Modules\Procurement\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\Concerns\BelongsToTenant;
use Modules\Global\Models\City;
use Modules\Global\Models\Province;
use Modules\Monitoring\Models\AuditLog;
use Modules\Monitoring\Models\FileUpload;

class Vendor extends Model
{
    /** @use HasFactory<Factory<static>> */
    use BelongsToTenant, HasFactory, SoftDeletes;

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

    /**
     * @return BelongsTo<Province, $this>
     */
    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    /**
     * @return BelongsTo<City, $this>
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * @return HasMany<PurchaseOrder, $this>
     */
    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    /**
     * @return HasMany<ProcurementItem, $this>
     */
    public function preferredProcurementItems(): HasMany
    {
        return $this->hasMany(ProcurementItem::class, 'preferred_vendor_id');
    }

    /**
     * @return HasMany<PurchaseRequisitionItem, $this>
     */
    public function preferredRequisitionItems(): HasMany
    {
        return $this->hasMany(PurchaseRequisitionItem::class, 'preferred_vendor_id');
    }

    /**
     * @return HasMany<RfqVendor, $this>
     */
    public function rfqVendors(): HasMany
    {
        return $this->hasMany(RfqVendor::class);
    }

    /**
     * @return HasMany<VendorBill, $this>
     */
    public function vendorBills(): HasMany
    {
        return $this->hasMany(VendorBill::class);
    }

    /**
     * @return MorphMany<AuditLog, $this>
     */
    public function auditLogs(): MorphMany
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }

    /**
     * @return MorphMany<FileUpload, $this>
     */
    public function fileUploads(): MorphMany
    {
        return $this->morphMany(FileUpload::class, 'fileable');
    }
}
