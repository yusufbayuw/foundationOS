<?php

namespace App\Console\Commands;

use App\Integrations\Moodle\MoodleClient;
use App\Integrations\Moodle\MoodleEnrollmentReconciler;
use App\Models\MoodleClassCourseMapping;
use Illuminate\Console\Command;
use Throwable;

class MoodleReconcileEnrollmentCommand extends Command
{
    protected $signature = 'fos:moodle:reconcile-enrollment
        {--tenant= : Filter tenant_id}
        {--class= : Filter class_id}
        {--dry-run : Audit only, no drift rows written}
        {--fix : Reserved for Fase 3.2 (auto-remediate via outbox)}';

    protected $description = 'Detect enrollment drift between FOS class_students and Moodle course enrollment.';

    public function handle(MoodleClient $client, MoodleEnrollmentReconciler $reconciler): int
    {
        if (! config('moodle.enabled', false)) {
            $this->warn('Moodle sync disabled.');

            return self::SUCCESS;
        }

        $dryRun = (bool) $this->option('dry-run');
        $tenantId = $this->option('tenant') !== null ? (int) $this->option('tenant') : null;
        $classId = $this->option('class') !== null ? (int) $this->option('class') : null;

        $query = MoodleClassCourseMapping::query()
            ->where('is_active', true);
        if ($tenantId !== null) {
            $query->where('tenant_id', $tenantId);
        }
        if ($classId !== null) {
            $query->where('class_id', $classId);
        }

        $mappings = $query->get();
        $summary = ['mappings_checked' => 0, 'drifts_detected' => 0];

        foreach ($mappings as $mapping) {
            $summary['mappings_checked']++;

            try {
                $payload = $client->call('core_enrol_get_enrolled_users', [
                    'courseid' => (int) $mapping->moodle_course_id,
                ]);
            } catch (Throwable $e) {
                $this->error("Mapping #{$mapping->id} fetch failed: {$e->getMessage()}");

                continue;
            }

            $drifts = $reconciler->reconcileMapping(
                $mapping,
                is_array($payload) ? $payload : [],
                dryRun: $dryRun,
            );

            $summary['drifts_detected'] += $drifts->count();
            $this->line(sprintf(
                '[mapping #%d] tenant=%d class=%d course=%d → %d drifts%s',
                $mapping->id, $mapping->tenant_id, $mapping->class_id,
                $mapping->moodle_course_id, $drifts->count(), $dryRun ? ' (dry-run)' : '',
            ));
        }

        $this->info(sprintf(
            'Done. Mappings checked: %d, drifts detected: %d%s',
            $summary['mappings_checked'], $summary['drifts_detected'],
            $dryRun ? ' (dry-run, not persisted)' : '',
        ));

        return self::SUCCESS;
    }
}
