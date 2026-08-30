<?php

namespace App\Http\Middleware;

use App\Models\PersonalAccessToken;
use App\Support\CurrentTenant;
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
            return $next($request);
        }

        if ($token->tenant_id === null) {
            if (config('tenancy.api_require_tenant', true)) {
                abort(403, 'API token must be scoped to a tenant.');
            }

            return $next($request);
        }

        $previousTeamId = getPermissionsTeamId();
        $user = $request->user();

        return $this->currentTenant->forTenant($token->tenant_id, function () use ($next, $previousTeamId, $request, $token, $user): Response {
            setPermissionsTeamId($token->tenant_id);
            $user?->unsetRelation('roles')->unsetRelation('permissions');

            try {
                return $next($request);
            } finally {
                setPermissionsTeamId($previousTeamId);
                $user?->unsetRelation('roles')->unsetRelation('permissions');
            }
        });
    }
}
