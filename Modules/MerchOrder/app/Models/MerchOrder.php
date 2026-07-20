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

class MerchOrder extends Model
{
    use BelongsToTenant, HasAuditTrail, HasFactory, SoftDeletes;

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
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
            'status' => ShopOrderStatus::class,
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function isPrintable(): bool
    {
        return $this->status === ShopOrderStatus::PickedUp;
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
