<?php

namespace App\Http\Middleware;

use App\Events\TenantSwitched;
use App\Support\CurrentTenant;
use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Bind the active Filament tenant into the container so non-Filament
 * code paths (jobs dispatched mid-request, observers, services) see it
 * via the CurrentTenant resolver.
 */
class BindTenantToContainer
{
    public function __construct(protected CurrentTenant $currentTenant) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (class_exists(Filament::class)) {
            try {
                $tenant = Filament::getTenant();

                if ($tenant !== null) {
                    $previousTenantId = $this->currentTenant->id();
                    $newTenantId = $tenant->getKey();

                    $this->currentTenant->set($tenant);

                    if ($previousTenantId !== $newTenantId) {
                        event(new TenantSwitched(
                            newTenantId: $newTenantId,
                            previousTenantId: $previousTenantId,
                            userId: auth()->id(),
                        ));
                    }
                }
            } catch (\Throwable) {
                // no-op: no tenant context available
            }
        }

        return $next($request);
    }
}
