<?php

namespace App\Console\Commands;

use App\Integrations\Moodle\Exceptions\MoodleIntegrationException;
use App\Integrations\Moodle\MoodleSyncService;
use Illuminate\Console\Command;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Core\Models\UserTenantRole;

class MoodleSyncCohortsCommand extends Command
{
    protected $signature = 'fos:moodle:sync-cohorts {--tenant= : Filter tenant_id}';

    protected $description = 'Provision Moodle cohorts per tenant and sync user memberships';

    public function handle(MoodleSyncService $syncService): int
    {
        if (! config('moodle.enabled', false)) {
            $this->warn('Moodle sync disabled.');
            return self::SUCCESS;
        }

        if (! config('moodle.cohort_sync_enabled', true)) {
            $this->warn('Cohort sync disabled. Set MOODLE_COHORT_SYNC_ENABLED=true.');
            return self::SUCCESS;
        }

        $tenantOption = $this->option('tenant');
        $tenantId = is_numeric($tenantOption) ? (int) $tenantOption : null;

        $tenantQuery = Tenant::query();
        if ($tenantId !== null) {
            $tenantQuery->whereKey($tenantId);
        }

        $syncedTenants = 0;
        $syncedMembers = 0;
        $failedTenants = 0;

        $tenantQuery->chunkById(100, function ($tenants) use ($syncService, &$syncedTenants, &$syncedMembers, &$failedTenants): void {
            foreach ($tenants as $tenant) {
                try {
                    $cohortId = $syncService->ensureTenantCohort((int) $tenant->id);
                    $syncedTenants++;

                    $assignments = UserTenantRole::query()
                        ->where('tenant_id', $tenant->id)
                        ->whereNull('deleted_at')
                        ->get(['user_id'])
                        ->unique('user_id');

                    foreach ($assignments as $assignment) {
                        /** @var User|null $user */
                        $user = User::withTrashed()->find((int) $assignment->user_id);
                        if (! $user) {
                            continue;
                        }

                        $moodleUserId = $syncService->upsertUser($user);
                        $syncService->syncTenantCohortMembership((int) $tenant->id, $moodleUserId);
                        $syncedMembers++;
                    }

                    $this->line("Tenant {$tenant->id} -> cohort {$cohortId} synced.");
                } catch (MoodleIntegrationException $exception) {
                    $failedTenants++;
                    $this->warn("Tenant {$tenant->id} cohort sync failed: {$exception->getMessage()}");
                }
            }
        });

        $this->info("Cohort sync done. Tenants: {$syncedTenants}, memberships synced: {$syncedMembers}, failed tenants: {$failedTenants}.");

        return $failedTenants > 0 ? self::FAILURE : self::SUCCESS;
    }
}
