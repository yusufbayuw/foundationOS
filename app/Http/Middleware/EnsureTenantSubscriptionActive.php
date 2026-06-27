<?php

namespace App\Http\Middleware;

use App\Support\CurrentTenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantSubscriptionActive
{
    public function __construct(protected CurrentTenant $currentTenant) {}

    public function handle(Request $request, Closure $next): Response
    {
        $tenant = $this->currentTenant->model();

        if (! $tenant) {
            return $next($request);
        }

        if ($tenant->isLocked()) {
            $billingPath = '/admin/'.$tenant->getRouteKey().'/billing';

            if (! $request->is('*billing*')) {
                return redirect($billingPath)
                    ->with('warning', 'Langganan Anda telah berakhir. Silakan perbarui pembayaran untuk melanjutkan akses.');
            }
        }

        return $next($request);
    }
}
