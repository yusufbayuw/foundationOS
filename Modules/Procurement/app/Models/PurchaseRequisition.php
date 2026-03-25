<?php

namespace Modules\Procurement\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;

class PurchaseRequisition extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'requested_by',
        'approved_by',
        'request_number',
        'request_date',
        'required_date',
        'priority',
        'justification',
        'total_items',
        'total_estimated_amount',
        'status',
        'approved_at',
        'rejection_reason',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'request_date' => 'date',
            'required_date' => 'date',
            'total_items' => 'integer',
            'total_estimated_amount' => 'decimal:2',
            'approved_at' => 'datetime',
        ];
    }

    public function tenant(): BelongsTo { return $this->belongsTo(Tenant::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function requester(): BelongsTo { return $this->belongsTo(User::class, 'requested_by'); }
    public function approver(): BelongsTo { return $this->belongsTo(User::class, 'approved_by'); }
    public function items(): HasMany { return $this->hasMany(PurchaseRequisitionItem::class); }
    public function rfqs(): HasMany { return $this->hasMany(RequestForQuotation::class); }
}
