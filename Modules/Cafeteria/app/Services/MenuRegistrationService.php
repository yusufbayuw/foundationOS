<?php

namespace Modules\Cafeteria\Services;

use Modules\Cafeteria\Exceptions\DuplicateMenuCodeException;
use Modules\Cafeteria\Models\Menu;

class MenuRegistrationService
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
    ): Menu {
        $normalizedCode = strtoupper(trim($code));

        if ($normalizedCode === '') {
            throw new \InvalidArgumentException('Menu code cannot be empty.');
        }

        if ($this->codeExists($tenantId, $organizationId, $normalizedCode)) {
            throw new DuplicateMenuCodeException($normalizedCode);
        }

        return Menu::withoutTenantScope()->create([
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
        return Menu::withoutTenantScope()
            ->where('tenant_id', $tenantId)
            ->where('code', $code)
            ->when(
                $organizationId !== null,
                fn ($query) => $query->where($query->getModel()->qualifyColumn('organization_id'), $organizationId),
                fn ($query) => $query->whereNull('organization_id'),
            )
            ->exists();
    }
}
