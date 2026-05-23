<?php

namespace Modules\Inventory\Enums;

enum ValuationMethod: string
{
    case Fifo = 'fifo';
    case Lifo = 'lifo';
    case Avg = 'avg';

    public function label(): string
    {
        return match ($this) {
            self::Fifo => 'FIFO',
            self::Lifo => 'LIFO',
            self::Avg => 'Average',
        };
    }
}
