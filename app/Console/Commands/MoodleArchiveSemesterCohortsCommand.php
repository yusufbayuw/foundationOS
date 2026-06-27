<?php

namespace App\Console\Commands;

use App\Integrations\Moodle\MoodleClient;
use App\Models\MoodleEntityMapping;
use Illuminate\Console\Command;
use Modules\Core\Models\AcademicPeriod;
use Modules\Core\Models\Tenant;

class MoodleArchiveSemesterCohortsCommand extends Command
{
    protected $signature = 'fos:moodle:archive-semester-cohorts
        {--tenant= : Limit to specific tenant_id}
        {--dry-run : Show what would be archived without calling Moodle}';

    protected $description = 'Archive Moodle course category for semesters whose end_date has passed';

    public function handle(MoodleClient $client): int
    {
        if (! config('moodle.enabled', false)) {
            $this->warn('Moodle sync disabled.');

            return self::SUCCESS;
        }

        $query = AcademicPeriod::withoutTenantScope()
            ->where('end_date', '<', now())
            ->where('is_active', false);

        if ($this->option('tenant')) {
            $query->where($query->getModel()->qualifyColumn('tenant_id'), $this->option('tenant'));
        }

        $periods = $query->cursor();

        $archived = 0;
        $skipped = 0;

        foreach ($periods as $period) {
            $tenant = Tenant::find($period->tenant_id);

            if (! $tenant) {
                continue;
            }

            $idnumber = "fos_semester_t{$tenant->id}_p{$period->id}";
            $mapping = MoodleEntityMapping::where('entity_type', 'semester_category')
                ->where('fos_entity_id', $period->id)
                ->where('tenant_id', $period->tenant_id)
                ->first();

            if (! $mapping) {
                $skipped++;

                continue;
            }

            $this->line("  Archiving semester category: {$period->name} (tenant: {$tenant->name})");

            if (! $this->option('dry-run')) {
                try {
                    $client->call('core_course_update_categories', [
                        'categories' => [[
                            'id' => $mapping->moodle_id,
                            'visible' => 0,
                        ]],
                    ]);

                    $archived++;
                } catch (\Throwable $e) {
                    $this->error("  Failed: {$e->getMessage()}");
                }
            } else {
                $archived++;
            }
        }

        $this->info(sprintf(
            '%s Archived %d semester category cohort(s), skipped %d (no Moodle mapping).',
            $this->option('dry-run') ? '[dry-run]' : '',
            $archived,
            $skipped,
        ));

        return self::SUCCESS;
    }
}
