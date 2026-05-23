<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantSubscriptionActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = Filament::getTenant();

        if (! $tenant) {
            return $next($request);
        }

        if ($tenant->isLocked()) {
            $billingPath = '/admin/'.($tenant->slug ?? $tenant->id).'/billing';

            if (! $request->is('*billing*')) {
                return redirect($billingPath)
                    ->with('warning', 'Langganan Anda telah berakhir. Silakan perbarui pembayaran untuk melanjutkan akses.');
            }
        }

        return $next($request);
    }
}
