<?php

namespace App\Console\Commands;

use App\Integrations\Moodle\MoodleClient;
use App\Integrations\Moodle\MoodleEnrollmentDriftFixer;
use App\Integrations\Moodle\MoodleEnrollmentReconciler;
use App\Models\MoodleClassCourseMapping;
use Illuminate\Console\Command;
use Throwable;

class MoodleReconcileEnrollmentCommand extends Command
{
    protected $signature = 'fos:moodle:reconcile-enrollment
        {--tenant= : Filter tenant_id}
        {--all-tenants : Reconcile across every tenant (cannot combine with --tenant)}
        {--class= : Filter class_id}
        {--dry-run : Audit only, no drift rows written}
        {--fix : Auto-remediate detected drifts per tenant setting moodle.enrollment_drift_auto_fix_mode}';

    protected $description = 'Detect and optionally auto-fix enrollment drift between FOS class_students and Moodle course enrollment.';

    public function handle(MoodleClient $client, MoodleEnrollmentReconciler $reconciler, MoodleEnrollmentDriftFixer $fixer): int
    {
        if (! config('moodle.enabled', false)) {
            $this->warn('Moodle sync disabled.');

            return self::SUCCESS;
        }

        $dryRun = (bool) $this->option('dry-run');
        $fix = (bool) $this->option('fix');
        $allTenants = (bool) $this->option('all-tenants');
        $tenantId = $this->option('tenant') !== null ? (int) $this->option('tenant') : null;
        $classId = $this->option('class') !== null ? (int) $this->option('class') : null;

        if ($dryRun && $fix) {
            $this->error('Cannot combine --dry-run with --fix.');

            return self::FAILURE;
        }
        if ($allTenants && $tenantId !== null) {
            $this->error('Cannot combine --all-tenants with --tenant.');

            return self::FAILURE;
        }

        $query = MoodleClassCourseMapping::query()
            ->where('is_active', true);
        if ($tenantId !== null) {
            $query->where('tenant_id', $tenantId);
        }
        if ($classId !== null) {
            $query->where('class_id', $classId);
        }

        $mappings = $query->get();
        $summary = ['mappings_checked' => 0, 'drifts_detected' => 0, 'drifts_fixed' => 0];

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

            if ($fix) {
                foreach ($drifts as $drift) {
                    if ($fixer->fix($drift)) {
                        $summary['drifts_fixed']++;
                    }
                }
            }

            $this->line(sprintf(
                '[mapping #%d] tenant=%d class=%d course=%d → %d drifts%s%s',
                $mapping->id, $mapping->tenant_id, $mapping->class_id,
                $mapping->moodle_course_id, $drifts->count(),
                $dryRun ? ' (dry-run)' : '',
                $fix ? sprintf(', fixed=%d', $summary['drifts_fixed']) : '',
            ));
        }

        $this->info(sprintf(
            'Done. Mappings checked: %d, drifts detected: %d%s%s',
            $summary['mappings_checked'], $summary['drifts_detected'],
            $dryRun ? ' (dry-run, not persisted)' : '',
            $fix ? sprintf(', drifts fixed: %d', $summary['drifts_fixed']) : '',
        ));

        return self::SUCCESS;
    }
}
