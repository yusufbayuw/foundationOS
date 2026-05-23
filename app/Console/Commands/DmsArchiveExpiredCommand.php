<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Dms\Models\Document;

class DmsArchiveExpiredCommand extends Command
{
    protected $signature = 'dms:archive-expired';

    protected $description = 'Archive documents whose retention period has expired';

    public function handle(): int
    {
        $count = Document::query()
            ->whereNotNull('retention_expires_at')
            ->whereDate('retention_expires_at', '<', now())
            ->where('status', '!=', 'archived')
            ->update(['status' => 'archived']);

        $this->info("Archived {$count} document(s).");

        return self::SUCCESS;
    }
}
