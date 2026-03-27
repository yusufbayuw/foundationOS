<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Library\Models\Loan;
use Modules\Library\Support\LibraryCirculationService;

class LibraryRecalculateFinesCommand extends Command
{
    protected $signature = 'fos:library:recalc-fines {--tenant= : Filter tenant_id} {--member= : Filter member_id}';

    protected $description = 'Recalculate overdue fines and member counters for library loans';

    public function handle(LibraryCirculationService $circulationService): int
    {
        $query = Loan::query()->with(['member', 'bookCopy.book']);

        $tenantId = is_numeric($this->option('tenant')) ? (int) $this->option('tenant') : null;
        $memberId = is_numeric($this->option('member')) ? (int) $this->option('member') : null;

        if ($tenantId !== null) {
            $query->where('tenant_id', $tenantId);
        }

        if ($memberId !== null) {
            $query->where('member_id', $memberId);
        }

        $processed = 0;

        $query->chunkById(100, function ($loans) use ($circulationService, &$processed): void {
            foreach ($loans as $loan) {
                $circulationService->recalculateLoanFine($loan);
                $processed++;
            }
        });

        $this->info("Library fine recalculation completed for {$processed} loan(s).");

        return self::SUCCESS;
    }
}
