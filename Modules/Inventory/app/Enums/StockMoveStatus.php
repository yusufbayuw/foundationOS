<?php

namespace Modules\Inventory\Enums;

enum StockMoveStatus: string
{
    case Draft = 'draft';
    case Committed = 'committed';
    case Cancelled = 'cancelled';
}
