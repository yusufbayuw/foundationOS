<?php

namespace Modules\Marketplace\Services;

use App\Support\TypedValue;
use Modules\Marketplace\Exceptions\DuplicateSellerCodeException;
use Modules\Marketplace\Models\Seller;

class SellerRegistrationService
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
        string $verificationStatus = 'pending',
    ): Seller {
        $normalizedCode = strtoupper(trim($code));

        if ($normalizedCode === '') {
            throw new \InvalidArgumentException('Seller code cannot be empty.');
        }

        if ($this->codeExists($tenantId, $organizationId, $normalizedCode)) {
            throw new DuplicateSellerCodeException($normalizedCode);
        }

        return Seller::withoutTenantScope()->create([
            'tenant_id' => $tenantId,
            'organization_id' => $organizationId,
            'code' => $normalizedCode,
            'name' => trim($name),
            'status' => 'active',
            'description' => $description,
            'meta' => $meta,
            'verification_status' => $verificationStatus,
        ]);
    }

    public function verify(Seller $seller): Seller
    {
        $seller->forceFill(['verification_status' => 'verified'])->save();

        return TypedValue::model($seller->fresh());
    }

    protected function codeExists(int $tenantId, ?int $organizationId, string $code): bool
    {
        return Seller::withoutTenantScope()
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
