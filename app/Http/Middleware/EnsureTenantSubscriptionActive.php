<?php

namespace App\Http\Middleware;

use App\Support\TypedValue;
use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Modules\Core\Models\Tenant;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantSubscriptionActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = Filament::getTenant();

        if (! $tenant instanceof Tenant) {
            return TypedValue::response($next($request));
        }

        if ($tenant->isLocked()) {
            $billingPath = '/admin/'.TypedValue::string($tenant->getRouteKey()).'/billing';

            if (! $request->is('*billing*')) {
                return redirect($billingPath)
                    ->with('warning', 'Langganan Anda telah berakhir. Silakan perbarui pembayaran untuk melanjutkan akses.');
            }
        }

        return TypedValue::response($next($request));
    }
}
