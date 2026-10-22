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
        // Tenant-bound jobs must never silently run in unrestricted CLI context
        // or accidentally inherit the previous worker job's tenant.
        if ($this->tenantId === null) {
            throw new MissingTenantContextException($job::class);
        }

        $tenant = Tenant::query()->find($this->tenantId);

        if ($tenant === null) {
            throw new \RuntimeException('Queued tenant is missing or deleted.');
        }

        // Do not block billing/recovery/cleanup jobs for suspended tenants.
        $previousTeamId = getPermissionsTeamId();

        return app(CurrentTenant::class)->forTenant($tenant, function () use ($job, $next, $previousTeamId): mixed {
            setPermissionsTeamId($this->tenantId);

            try {
                return $next($job);
            } finally {
                setPermissionsTeamId($previousTeamId);
            }
        });
    }
}
