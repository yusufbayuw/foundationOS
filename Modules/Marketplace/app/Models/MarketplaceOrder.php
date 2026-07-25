<?php

namespace Modules\Marketplace\Models;

use App\Enums\ShopOrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use InvalidArgumentException;
use Modules\Core\Models\Concerns\BelongsToTenant;
use Modules\Core\Models\User;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class MarketplaceOrder extends Model
{
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['code', 'status', 'name'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    use BelongsToTenant, LogsActivity;

    protected $table = 'marketplace_orders';

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
        'meta',
        'seller_id',
    ];

    public function getCustomerNameAttribute(): ?string
    {
        return $this->metaValue('customer_name') ?? $this->metaValue('user_name') ?? $this->name;
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
        return is_array($this->meta) ? ($this->meta[$key] ?? null) : null;
    }

    protected function casts(): array
    {
        return [
            'meta' => 'array',
            'status' => ShopOrderStatus::class,
        ];
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function markReadyForPickup(): void
    {
        $this->transitionTo(ShopOrderStatus::ReadyForPickup);
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
        if (! $this->status->canTransitionTo($nextStatus)) {
            throw new InvalidArgumentException(sprintf(
                'Cannot transition shop order from %s to %s.',
                $this->status->value,
                $nextStatus->value,
            ));
        }

        $this->forceFill([
            'status' => $nextStatus,
            'rejection_reason' => $nextStatus === ShopOrderStatus::Rejected ? $rejectionReason : null,
        ])->save();
    }
}
