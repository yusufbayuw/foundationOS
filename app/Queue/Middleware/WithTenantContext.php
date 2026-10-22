<?php

namespace App\Queue\Middleware;

use App\Exceptions\MissingTenantContextException;
use App\Support\CurrentTenant;
use Closure;
use Modules\Core\Models\Tenant;

class WithTenantContext
{
    public function __construct(public int|string|null $tenantId) {}

    public function handle(object $job, Closure $next): mixed
    {
        // Only explicitly reviewed jobs may run without a tenant. They operate
        // from an isolated NULL context; a previous worker tenant never leaks.
        $isTenantlessSystemJob = $this->tenantId === null
            && in_array($job::class, config('tenancy.queue_tenantless_job_types', []), true);

        if ($this->tenantId === null && ! $isTenantlessSystemJob) {
            throw new MissingTenantContextException($job::class);
        }

        $tenant = $this->tenantId === null ? null : Tenant::query()->find($this->tenantId);

        if ($this->tenantId !== null && $tenant === null) {
            throw new \RuntimeException('Queued tenant is missing or deleted.');
        }

        // Suspended tenants may still require recovery/cleanup jobs. Even for
        // explicitly global jobs, reset the Spatie permission team for the call.
        $previousTeamId = getPermissionsTeamId();

        return app(CurrentTenant::class)->forTenant($tenant, function () use ($job, $next, $previousTeamId, $tenant): mixed {
            setPermissionsTeamId($tenant?->getKey() ?? 0);

            try {
                return $next($job);
            } finally {
                setPermissionsTeamId($previousTeamId);
            }
        });
    }
}
