<?php

namespace Modules\Inventory\Listeners;

use Modules\Inventory\Services\GoodsReceiptStockService;
use Modules\Procurement\Events\GoodsReceiptConfirmed;

class CreateStockMovesFromGoodsReceipt
{
    public function __construct(
        private readonly GoodsReceiptStockService $stockService,
    ) {}

    public function handle(GoodsReceiptConfirmed $event): void
    {
        if ($event->goodsReceipt->status !== 'confirmed') {
            return;
        }

        $this->stockService->receiveFromGoodsReceipt($event->goodsReceipt);
    }
}
