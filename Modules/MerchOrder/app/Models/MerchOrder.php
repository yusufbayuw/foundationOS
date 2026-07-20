<?php

namespace Modules\MerchOrder\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\Concerns\BelongsToTenant;
use Modules\Core\Models\Organization;
use Modules\Monitoring\Models\Concerns\HasAuditTrail;

class MerchOrder extends Model
{
    use BelongsToTenant, HasAuditTrail, HasFactory, SoftDeletes;

    protected $table = 'merch_orders';

    protected $fillable = [
        'tenant_id',
        'organization_id',
        'code',
        'name',
        'status',
        'description',
        'total_amount',
        'payment_reference',
        'paid_at',
        'ready_for_pickup_at',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'paid_at' => 'datetime',
            'ready_for_pickup_at' => 'datetime',
            'meta' => 'array',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function markReadyForPickup(): bool
    {
        if ((string) $this->status !== 'paid') {
            return false;
        }

        return $this->forceFill([
            'status' => 'ready_for_pickup',
            'ready_for_pickup_at' => now(),
        ])->save();
    }

    public function isPrintable(): bool
    {
        return (string) $this->status === 'active';
    }
}
