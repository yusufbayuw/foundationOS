<?php

namespace Modules\Sales\Services;

use Modules\Sales\Exceptions\DuplicateCustomerCodeException;
use Modules\Sales\Models\Customer;

class CustomerRegistrationService
{
    public function register(
        int $tenantId,
        ?int $organizationId,
        string $code,
        string $name,
        ?string $email = null,
        ?string $phone = null,
        ?string $address = null,
        bool $isCooperativeMember = false,
        ?string $memberNumber = null,
        float $memberDiscountPercent = 0,
    ): Customer {
        $normalizedCode = strtoupper(trim($code));

        if ($normalizedCode === '') {
            throw new \InvalidArgumentException('Customer code cannot be empty.');
        }

        if ($this->codeExists($tenantId, $organizationId, $normalizedCode)) {
            throw new DuplicateCustomerCodeException($normalizedCode);
        }

        return Customer::withoutTenantScope()->create([
            'tenant_id' => $tenantId,
            'organization_id' => $organizationId,
            'code' => $normalizedCode,
            'name' => trim($name),
            'email' => $email !== null ? strtolower(trim($email)) : null,
            'phone' => $phone !== null ? trim($phone) : null,
            'address' => $address,
            'is_active' => true,
            'is_cooperative_member' => $isCooperativeMember,
            'member_number' => $memberNumber,
            'member_discount_percent' => $memberDiscountPercent,
        ]);
    }

    public function deactivate(Customer $customer): Customer
    {
        $customer->forceFill(['is_active' => false])->save();

        return $customer->fresh();
    }

    protected function codeExists(int $tenantId, ?int $organizationId, string $code): bool
    {
        return Customer::withoutTenantScope()
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
