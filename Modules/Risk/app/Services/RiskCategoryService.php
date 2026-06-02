<?php

namespace Modules\Risk\Services;

use Modules\Risk\Exceptions\DuplicateRiskCategoryCodeException;
use Modules\Risk\Models\RiskCategory;

class RiskCategoryService
{
    /**
     * Register an active risk category with a tenant-unique code.
     *
     * @param  array<string, mixed>  $meta
     */
    public function register(
        int $tenantId,
        ?int $organizationId,
        string $code,
        string $name,
        ?string $description = null,
        array $meta = [],
    ): RiskCategory {
        $normalizedCode = strtoupper(trim($code));

        if ($normalizedCode === '') {
            throw new \InvalidArgumentException('Risk category code cannot be empty.');
        }

        if ($this->codeExists($tenantId, $organizationId, $normalizedCode)) {
            throw new DuplicateRiskCategoryCodeException($normalizedCode);
        }

        return RiskCategory::withoutTenantScope()->create([
            'tenant_id' => $tenantId,
            'organization_id' => $organizationId,
            'code' => $normalizedCode,
            'name' => trim($name),
            'status' => 'active',
            'description' => $description,
            'meta' => $meta,
        ]);
    }

    public function deactivate(RiskCategory $category): RiskCategory
    {
        $category->forceFill(['status' => 'inactive'])->save();

        return $category->fresh();
    }

    public function reactivate(RiskCategory $category): RiskCategory
    {
        $category->forceFill(['status' => 'active'])->save();

        return $category->fresh();
    }

    protected function codeExists(int $tenantId, ?int $organizationId, string $code): bool
    {
        return RiskCategory::withoutTenantScope()
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
