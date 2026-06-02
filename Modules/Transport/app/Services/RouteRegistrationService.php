<?php

namespace Modules\Transport\Services;

use Modules\Transport\Exceptions\DuplicateRouteCodeException;
use Modules\Transport\Models\Route;

class RouteRegistrationService
{
    /**
     * @param  array<string, mixed>  $meta
     */
    public function register(
        int $tenantId,
        ?int $organizationId,
        string $code,
        string $name,
        ?string $description = null,
        array $meta = [],
        string $status = 'active',
    ): Route {
        $normalizedCode = strtoupper(trim($code));

        if ($normalizedCode === '') {
            throw new \InvalidArgumentException('Route code cannot be empty.');
        }

        if ($this->codeExists($tenantId, $organizationId, $normalizedCode)) {
            throw new DuplicateRouteCodeException($normalizedCode);
        }

        return Route::withoutTenantScope()->create([
            'tenant_id' => $tenantId,
            'organization_id' => $organizationId,
            'code' => $normalizedCode,
            'name' => trim($name),
            'status' => $status,
            'description' => $description,
            'meta' => $meta,
        ]);
    }

    protected function codeExists(int $tenantId, ?int $organizationId, string $code): bool
    {
        return Route::withoutTenantScope()
            ->where('tenant_id', $tenantId)
            ->where('code', $code)
            ->when(
                $organizationId !== null,
                fn ($query) => $query->where('organization_id', $organizationId),
                fn ($query) => $query->whereNull('organization_id'),
            )
            ->exists();
    }
}
