<?php

namespace App\Http\Middleware;

use App\Models\PersonalAccessToken;
use App\Support\CurrentTenant;
use App\Support\TypedValue;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveApiTenant
{
    public function __construct(protected CurrentTenant $currentTenant) {}

    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->user()?->currentAccessToken();

        if (! $token instanceof PersonalAccessToken) {
            return TypedValue::response($next($request));
        }

        if ($token->tenant_id === null) {
            if (config('tenancy.api_require_tenant', true)) {
                abort(403, 'API token must be scoped to a tenant.');
            }

            return TypedValue::response($next($request));
        }

        $this->currentTenant->set($token->tenant_id);

        return TypedValue::response($next($request));
    }
}
