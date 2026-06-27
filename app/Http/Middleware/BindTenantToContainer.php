<?php

namespace App\Http\Middleware;

use App\Events\TenantSwitched;
use App\Support\CurrentTenant;
use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Bind the active Filament panel tenant into CurrentTenant.
 *
 * This is the sole Filament → application bridge for tenant resolution.
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
                } else {
                    $this->currentTenant->forget();
                }
            } catch (\Throwable) {
                $this->currentTenant->forget();
            }
        }

        return $next($request);
    }
}
