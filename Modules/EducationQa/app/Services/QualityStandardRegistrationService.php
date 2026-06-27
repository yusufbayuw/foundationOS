<?php

namespace Modules\EducationQa\Services;

use Modules\EducationQa\Exceptions\DuplicateQualityStandardCodeException;
use Modules\EducationQa\Models\QualityStandard;

class QualityStandardRegistrationService
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
    ): QualityStandard {
        $normalizedCode = strtoupper(trim($code));

        if ($normalizedCode === '') {
            throw new \InvalidArgumentException('Quality standard code cannot be empty.');
        }

        if ($this->codeExists($tenantId, $organizationId, $normalizedCode)) {
            throw new DuplicateQualityStandardCodeException($normalizedCode);
        }

        return QualityStandard::withoutTenantScope()->create([
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
        return QualityStandard::withoutTenantScope()
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
