<?php

namespace App\Enums;

enum ShopOrderStatus: string
{
    case PendingPayment = 'pending_payment';
    case Paid = 'paid';
    case ReadyForPickup = 'ready_for_pickup';
    case PickedUp = 'picked_up';
    case Rejected = 'rejected';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PendingPayment => 'Pending payment',
            self::Paid => 'Paid',
            self::ReadyForPickup => 'Ready for pickup',
            self::PickedUp => 'Picked up',
            self::Rejected => 'Rejected',
            self::Cancelled => 'Cancelled',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $status): array => [$status->value => $status->label()])
            ->all();
    }

    public function canTransitionTo(self $next): bool
    {
        return in_array($next, $this->allowedTransitions(), true);
    }

    /**
     * @return list<self>
     */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::PendingPayment => [self::Paid, self::Rejected, self::Cancelled],
            self::Paid => [self::ReadyForPickup, self::Rejected, self::Cancelled],
            self::ReadyForPickup => [self::PickedUp, self::Cancelled],
            self::PickedUp, self::Rejected, self::Cancelled => [],
        };
    }
}
