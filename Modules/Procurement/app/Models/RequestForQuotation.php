<?php

namespace Modules\Procurement\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;

class RequestForQuotation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'purchase_requisition_id',
        'created_by',
        'rfq_number',
        'rfq_date',
        'closing_date',
        'description',
        'total_estimated_budget',
        'currency',
        'status',
        'award_criteria',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'rfq_date' => 'date',
            'closing_date' => 'date',
            'total_estimated_budget' => 'decimal:2',
        ];
    }

    public function tenant(): BelongsTo { return $this->belongsTo(Tenant::class); }
    public function purchaseRequisition(): BelongsTo { return $this->belongsTo(PurchaseRequisition::class); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function vendors(): HasMany { return $this->hasMany(RfqVendor::class); }
    public function items(): HasMany { return $this->hasMany(RfqItem::class); }
    public function purchaseOrders(): HasMany { return $this->hasMany(PurchaseOrder::class); }
}
