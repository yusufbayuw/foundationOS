<?php

namespace Modules\ItOps\Services;

use Modules\ItOps\Exceptions\DuplicateSoftwareLicenseCodeException;
use Modules\ItOps\Models\SoftwareLicense;

class SoftwareLicenseRegistrationService
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
    ): SoftwareLicense {
        $normalizedCode = strtoupper(trim($code));

        if ($normalizedCode === '') {
            throw new \InvalidArgumentException('Software license code cannot be empty.');
        }

        if ($this->codeExists($tenantId, $organizationId, $normalizedCode)) {
            throw new DuplicateSoftwareLicenseCodeException($normalizedCode);
        }

        return SoftwareLicense::withoutTenantScope()->create([
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
        return SoftwareLicense::withoutTenantScope()
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
