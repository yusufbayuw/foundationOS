<?php

namespace Modules\Inventory\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Inventory\Models\StockMove;

class StockMoveCommitted
{
    use Dispatchable, SerializesModels;

    public function __construct(public StockMove $stockMove) {}
}
