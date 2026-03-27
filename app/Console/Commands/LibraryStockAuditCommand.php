<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Library\Models\Book;
use Modules\Library\Models\Loan;

class LibraryStockAuditCommand extends Command
{
    protected $signature = 'fos:library:stock-audit {--tenant= : Filter tenant_id}';

    protected $description = 'Audit catalog stock against active loans and copy status';

    public function handle(): int
    {
        $tenantId = is_numeric($this->option('tenant')) ? (int) $this->option('tenant') : null;
        $query = Book::query()->withCount('copies');

        if ($tenantId !== null) {
            $query->where('tenant_id', $tenantId);
        }

        $rows = [];

        $query->chunkById(100, function ($books) use (&$rows): void {
            foreach ($books as $book) {
                $borrowed = Loan::query()
                    ->whereNull('return_date')
                    ->whereIn('status', ['borrowed', 'overdue'])
                    ->whereHas('bookCopy', fn ($query) => $query->where('book_id', $book->id))
                    ->count();

                $expectedAvailable = max(0, (int) $book->copies_count - $borrowed);

                if ((int) $book->total_copies !== (int) $book->copies_count || (int) $book->available_copies !== $expectedAvailable) {
                    $rows[] = [
                        $book->id,
                        $book->title,
                        (string) $book->total_copies,
                        (string) $book->copies_count,
                        (string) $book->available_copies,
                        (string) $expectedAvailable,
                    ];
                }
            }
        });

        if ($rows === []) {
            $this->info('Library stock audit completed. No mismatches found.');

            return self::SUCCESS;
        }

        $this->table(
            ['Book ID', 'Title', 'Recorded Total', 'Actual Copies', 'Recorded Available', 'Expected Available'],
            $rows,
        );

        $this->warn('Stock mismatches found. Review the rows above.');

        return self::FAILURE;
    }
}
