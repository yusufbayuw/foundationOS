<?php

namespace Modules\Core\Support\Tenancy;

use Filament\Facades\Filament;
use Modules\Core\Models\Tenant;

/**
 * Singleton resolver for the active tenant.
 *
 * Resolution priority:
 *   1. Manually set value (via set() / forTenant()) — used by queues, console, tests.
 *   2. Filament panel tenant (Filament::getTenant()).
 *
 * When no tenant context exists, returns null so global scopes become a no-op
 * and cross-tenant CLI/seeder operations remain unrestricted.
 */
class CurrentTenant
{
    protected int|string|null $tenantId = null;

    protected bool $manuallySet = false;

    public function set(int|string|Tenant|null $tenant): void
    {
        if ($tenant instanceof Tenant) {
            $this->tenantId = $tenant->getKey();
        } else {
            $this->tenantId = $tenant;
        }

        $this->manuallySet = true;
    }

    public function forget(): void
    {
        $this->tenantId = null;
        $this->manuallySet = false;
    }

    public function id(): int|string|null
    {
        if ($this->manuallySet) {
            return $this->tenantId;
        }

        return $this->resolveFromFilament();
    }

    /**
     * Run the given callback while the given tenant is active, then restore prior state.
     *
     * @template TReturn
     *
     * @param  \Closure():TReturn  $callback
     * @return TReturn
     */
    public function forTenant(int|string|Tenant|null $tenant, \Closure $callback): mixed
    {
        $previousId = $this->tenantId;
        $previouslySet = $this->manuallySet;

        $this->set($tenant);

        try {
            return $callback();
        } finally {
            $this->tenantId = $previousId;
            $this->manuallySet = $previouslySet;
        }
    }

    protected function resolveFromFilament(): int|string|null
    {
        if (! class_exists(Filament::class)) {
            return null;
        }

        try {
            return Filament::getTenant()?->getKey();
        } catch (\Throwable) {
            return null;
        }
    }
}
