<?php

namespace Modules\Inventory\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Inventory\Events\StockMoveCommitted;
use Modules\Inventory\Listeners\CommitStockAdjustmentOnWorkflowApproval;
use Modules\Inventory\Listeners\CreateStockMovesFromGoodsReceipt;
use Modules\Inventory\Listeners\PostStockMoveJournal;
use Modules\Procurement\Events\GoodsReceiptConfirmed;
use Modules\Workflow\Events\WorkflowAdvanced;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array<string, array<int, string>>
     */
    protected $listen = [
        StockMoveCommitted::class => [
            PostStockMoveJournal::class,
        ],
        GoodsReceiptConfirmed::class => [
            CreateStockMovesFromGoodsReceipt::class,
        ],
        WorkflowAdvanced::class => [
            CommitStockAdjustmentOnWorkflowApproval::class,
        ],
    ];

    /**
     * Indicates if events should be discovered.
     *
     * @var bool
     */
    protected static $shouldDiscoverEvents = true;

    /**
     * Configure the proper event listeners for email verification.
     */
    protected function configureEmailVerification(): void {}
}
