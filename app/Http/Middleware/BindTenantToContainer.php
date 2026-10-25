<?php

namespace App\Http\Middleware;

use App\Events\TenantSwitched;
use App\Support\CurrentTenant;
use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BindTenantToContainer
{
    public function __construct(protected CurrentTenant $currentTenant) {}

    public function handle(Request $request, Closure $next): Response
    {
        $tenant = null;

        if (class_exists(Filament::class)) {
            try {
                $tenant = Filament::getTenant();
            } catch (\Throwable) {
                // Non-tenant panel routes may not have a resolvable tenant.
            }
        }

        $previousTenantId = $this->currentTenant->id();
        $previousTeamId = getPermissionsTeamId();

        // Explicitly bind null on non-tenant routes, rather than accidentally
        // inheriting the preceding request's tenant in persistent processes.
        return $this->currentTenant->forTenant($tenant, function () use ($tenant, $previousTenantId, $previousTeamId, $next, $request): Response {
            try {
                // Reset permission teams for tenantless pages as well: a stale
                // team must not be inherited by registration/login requests.
                setPermissionsTeamId($tenant?->getKey() ?? 0);

                if ($tenant !== null) {
                    if ((string) $previousTenantId !== (string) $tenant->getKey()) {
                        event(new TenantSwitched(
                            newTenantId: $tenant->getKey(),
                            previousTenantId: $previousTenantId,
                            userId: auth()->id(),
                        ));
                    }
                }

                return $next($request);
            } finally {
                setPermissionsTeamId($previousTeamId);
            }
        });
    }
}
