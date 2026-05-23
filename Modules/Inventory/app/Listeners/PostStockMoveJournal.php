<?php

namespace Modules\Inventory\Listeners;

use Modules\Inventory\Events\StockMoveCommitted;
use Modules\Inventory\Services\StockJournalService;
use Modules\Monitoring\Services\AuditTrailRecorder;

class PostStockMoveJournal
{
    public function __construct(
        private readonly StockJournalService $journalService,
    ) {}

    public function handle(StockMoveCommitted $event): void
    {
        if ($event->stockMove->journal_entry_id) {
            return;
        }

        try {
            $journal = $this->journalService->postForMove($event->stockMove);
            $move = $event->stockMove->fresh();

            AuditTrailRecorder::record(
                $move,
                'stock_move.journal_posted',
                null,
                ['journal_entry_id' => $journal->getKey()],
                'Stock move journal entry posted.',
            );
        } catch (\RuntimeException) {
            // Skip when inventory/COGS accounts are not configured yet.
        }
    }
}
