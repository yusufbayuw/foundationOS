<?php

namespace Modules\Core\Support\Tenancy;

use Modules\Core\Exceptions\MissingTenantContextException;
use Modules\Core\Models\Tenant;

/**
 * Singleton resolver for the active tenant.
 *
 * Tenant context is bound explicitly by entry-point middleware and runners:
 * - Filament: BindTenantToContainer (from Filament panel tenant)
 * - API: ResolveApiTenant (from Sanctum token tenant_id)
 * - Queue: WithTenantContext (from InteractsWithTenant)
 * - CLI: tenant:run (CurrentTenant::forTenant)
 * - Tests: explicit set() / forget()
 *
 * Application code must read tenant context only through this class.
 */
class CurrentTenant
{
    protected int|string|null $tenantId = null;

    protected bool $manuallySet = false;

    protected ?Tenant $tenantModel = null;

    public function set(int|string|Tenant|null $tenant): void
    {
        if ($tenant instanceof Tenant) {
            $this->tenantId = $tenant->getKey();
            $this->tenantModel = $tenant;
        } else {
            $this->tenantId = $tenant;
            $this->tenantModel = null;
        }

        $this->manuallySet = $tenant !== null;
    }

    public function forget(): void
    {
        $this->tenantId = null;
        $this->tenantModel = null;
        $this->manuallySet = false;
    }

    public function isBound(): bool
    {
        return $this->manuallySet && $this->tenantId !== null;
    }

    public function id(): int|string|null
    {
        return $this->tenantId;
    }

    /**
     * @throws MissingTenantContextException
     */
    public function requiredId(): int|string
    {
        $tenantId = $this->id();

        if ($tenantId === null) {
            throw new MissingTenantContextException('tenant context');
        }

        return $tenantId;
    }

    public function model(): ?Tenant
    {
        if (! $this->isBound()) {
            return null;
        }

        if ($this->tenantModel !== null && (string) $this->tenantModel->getKey() === (string) $this->tenantId) {
            return $this->tenantModel;
        }

        return $this->tenantModel = Tenant::query()->find($this->tenantId);
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
        $previousModel = $this->tenantModel;
        $previouslySet = $this->manuallySet;

        $this->set($tenant);

        try {
            return $callback();
        } finally {
            $this->tenantId = $previousId;
            $this->tenantModel = $previousModel;
            $this->manuallySet = $previouslySet;
        }
    }
}
