<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Alumni\Models\Alumnus;

class AlumniTracerStudyBlastCommand extends Command
{
    protected $signature = 'alumni:tracer-study-blast {--tenant=}';

    protected $description = 'Queue tracer study outreach for alumni records';

    public function handle(): int
    {
        $query = Alumnus::query()->where('status', 'active');

        if ($tenantId = $this->option('tenant')) {
            $query->where('tenant_id', $tenantId);
        }

        $count = 0;
        $query->each(function (Alumnus $alumnus) use (&$count): void {
            $alumnus->update([
                'meta' => array_merge($alumnus->meta ?? [], [
                    'tracer_study_blast_at' => now()->toIso8601String(),
                ]),
            ]);
            $count++;
        });

        $this->info("Marked {$count} alumnus record(s) for tracer study outreach.");

        return self::SUCCESS;
    }
}
