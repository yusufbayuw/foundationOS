<?php

namespace App\Http\Middleware;

use App\Models\PersonalAccessToken;
use App\Models\TenantServiceIntegration;
use App\Support\CurrentTenant;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Symfony\Component\HttpFoundation\Response;

class ResolveApiTenant
{
    /**
     * Machine principals may only read the organization directory.
     * New service endpoints require an explicit privacy and permission review.
     */
    private const SERVICE_READ_ROUTES = [
        'api/v1/organizations',
        'api/v1/organizations/{organization}',
        'api/v2/organizations',
        'api/v2/organizations/{organization}',
    ];

    public function __construct(protected CurrentTenant $currentTenant) {}

    public function handle(Request $request, Closure $next): Response
    {
        $principal = $request->user();
        $token = $principal?->currentAccessToken();

        if (! $token instanceof PersonalAccessToken || ! $token->exists) {
            // Preserve Sanctum::actingAs() fixtures, never permit an unscoped
            // web session to bypass tenant checks on real API requests.
            if (app()->runningUnitTests() && config('tenancy.api_allow_transient_test_token', true)) {
                return $next($request);
            }

            return $this->deny('tenant_token_required', 'A persisted tenant-scoped API token is required.');
        }

        if ($token->tenant_id === null) {
            if (config('tenancy.api_require_tenant', true)) {
                return $this->deny('tenant_token_required', 'API token must be scoped to a tenant.');
            }

            return $next($request);
        }

        $tenant = $token->tenant;

        if (! $tenant instanceof Tenant || ! $this->subscriptionAllowsAccess($tenant)) {
            return $this->deny('tenant_subscription_inactive', 'Tenant subscription is unavailable.');
        }

        $previousTeamId = getPermissionsTeamId();

        return $this->currentTenant->forTenant($tenant, function () use ($next, $previousTeamId, $principal, $request, $tenant, $token): Response {
            setPermissionsTeamId($tenant->getKey());
            $principal?->unsetRelation('roles')->unsetRelation('permissions');

            try {
                if ($principal instanceof User) {
                    if (config('tenancy.api_enforce_membership', true)
                        && ! $principal->canAccessTenant($tenant)
                        && ! ($principal->isGlobalSuperAdmin()
                            && in_array('platform:tenants', $token->abilities ?? [], true))) {
                        return $this->deny('tenant_membership_required', 'Active tenant membership is required.');
                    }
                } elseif ($principal instanceof TenantServiceIntegration) {
                    // Re-read mutable service state; revocation must not rely on a cached auth actor.
                    $service = $principal->fresh();

                    if ($service === null
                        || (string) $service->tenant_id !== (string) $tenant->getKey()
                        || ! $service->isActive()
                        || $token->expires_at === null
                        || ! in_array('api:read', $token->abilities ?? [], true)
                        || ! in_array($request->method(), ['GET', 'HEAD'], true)
                        || ! in_array($request->route()?->uri(), self::SERVICE_READ_ROUTES, true)) {
                        return $this->deny('service_integration_denied', 'Service integration is inactive or not authorized for this route.');
                    }
                } else {
                    return $this->deny('tenant_principal_invalid', 'Unrecognized tenant API principal.');
                }

                return $next($request);
            } finally {
                setPermissionsTeamId($previousTeamId);
                $principal?->unsetRelation('roles')->unsetRelation('permissions');
            }
        });
    }

    private function subscriptionAllowsAccess(Tenant $tenant): bool
    {
        return match ($tenant->status) {
            'active' => $tenant->subscription_expires_at === null || $tenant->subscription_expires_at->isFuture(),
            'trial' => $tenant->trial_ends_at === null || $tenant->trial_ends_at->isFuture(),
            'past_due' => $tenant->isInGracePeriod(),
            default => false,
        };
    }

    private function deny(string $code, string $message): JsonResponse
    {
        return response()->json([
            'error' => ['code' => $code, 'message' => $message],
        ], 403);
    }
}
