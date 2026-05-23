<?php

namespace Modules\Inventory\Enums;

enum StockAdjustmentReason: string
{
    case Damage = 'damage';
    case Expired = 'expired';
    case CountVariance = 'count_variance';
    case Found = 'found';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Damage => 'Damage',
            self::Expired => 'Expired',
            self::CountVariance => 'Count Variance',
            self::Found => 'Found Stock',
            self::Other => 'Other',
        };
    }
}
