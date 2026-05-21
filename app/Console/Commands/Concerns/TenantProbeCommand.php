<?php

namespace App\Console\Commands\Concerns;

use App\Support\CurrentTenant;
use Illuminate\Console\Command;

/**
 * Internal probe used by JobRestoresTenantContextTest to assert that
 * `tenant:run` actually binds CurrentTenant for the wrapped command.
 *
 * Registered manually in the test (not auto-discovered) to avoid polluting
 * production artisan listings.
 */
class TenantProbeCommand extends Command
{
    protected $signature = 'tests:tenant-probe';

    protected $description = 'Internal: records CurrentTenant id at execution time.';

    public static int|string|null $observedTenantId = null;

    public function handle(CurrentTenant $currentTenant): int
    {
        self::$observedTenantId = $currentTenant->id();

        return self::SUCCESS;
    }
}
