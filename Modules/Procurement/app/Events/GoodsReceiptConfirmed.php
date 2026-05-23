<?php

namespace Modules\Procurement\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Procurement\Models\GoodsReceipt;

class GoodsReceiptConfirmed
{
    use Dispatchable, SerializesModels;

    public function __construct(public GoodsReceipt $goodsReceipt) {}
}
