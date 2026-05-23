<?php

namespace Modules\Inventory\Enums;

enum StockMoveType: string
{
    case In = 'in';
    case Out = 'out';

    public function label(): string
    {
        return match ($this) {
            self::In => 'Stock In',
            self::Out => 'Stock Out',
        };
    }
}
