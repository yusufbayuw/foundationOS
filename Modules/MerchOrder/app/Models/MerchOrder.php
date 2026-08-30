<?php

namespace Modules\MerchOrder\Models;

use App\Enums\ShopOrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use InvalidArgumentException;
use Modules\Core\Models\Concerns\BelongsToTenant;
use Modules\Core\Models\Organization;
use Modules\Core\Models\User;
use Modules\Monitoring\Models\Concerns\HasAuditTrail;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class MerchOrder extends Model
{
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['code', 'status', 'name'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    use BelongsToTenant, HasAuditTrail, HasFactory, LogsActivity, SoftDeletes;

    protected $table = 'merch_orders';

    protected $attributes = [
        'status' => 'pending_payment',
    ];

    protected $fillable = [
        'tenant_id',
        'organization_id',
        'code',
        'name',
        'status',
        'user_id',
        'rejection_reason',
        'description',
        'total_amount',
        'payment_reference',
        'paid_at',
        'ready_for_pickup_at',
        'meta',
    ];

    public function getCustomerNameAttribute(): ?string
    {
        $name = $this->getAttribute('name');

        return $this->metaValue('customer_name')
            ?? $this->metaValue('user_name')
            ?? (is_string($name) ? $name : null);
    }

    public function getTotalAttribute(): mixed
    {
        return $this->metaValue('total') ?? $this->metaValue('total_amount');
    }

    public function getPickupStatusAttribute(): ?string
    {
        return $this->metaValue('pickup_status');
    }

    public function getPickupDateAttribute(): mixed
    {
        return $this->metaValue('pickup_date') ?? $this->metaValue('picked_up_at');
    }

    protected function metaValue(string $key): mixed
    {
        $meta = $this->getAttribute('meta');

        return is_array($meta) ? ($meta[$key] ?? null) : null;
    }

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'paid_at' => 'datetime',
            'ready_for_pickup_at' => 'datetime',
            'meta' => 'array',
            'status' => ShopOrderStatus::class,
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function markReadyForPickup(): bool
    {
        if ($this->getAttribute('status') !== ShopOrderStatus::Paid) {
            return false;
        }

        return $this->forceFill([
            'status' => ShopOrderStatus::ReadyForPickup,
            'ready_for_pickup_at' => now(),
        ])->save();
    }

    public function isPrintable(): bool
    {
        return $this->getAttribute('status') === ShopOrderStatus::PickedUp;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function markPickedUp(): void
    {
        $this->transitionTo(ShopOrderStatus::PickedUp);
    }

    public function reject(string $reason): void
    {
        $this->transitionTo(ShopOrderStatus::Rejected, $reason);
    }

    public function transitionTo(ShopOrderStatus $nextStatus, ?string $rejectionReason = null): void
    {
        $currentStatus = $this->getAttribute('status');

        if (! $currentStatus instanceof ShopOrderStatus) {
            throw new InvalidArgumentException('Merch order has an invalid status.');
        }

        if (! $currentStatus->canTransitionTo($nextStatus)) {
            throw new InvalidArgumentException(sprintf(
                'Cannot transition shop order from %s to %s.',
                $currentStatus->value,
                $nextStatus->value,
            ));
        }

        $this->forceFill([
            'status' => $nextStatus,
            'rejection_reason' => $nextStatus === ShopOrderStatus::Rejected ? $rejectionReason : null,
        ])->save();
    }
}
