<?php

namespace Modules\Donation\Services;

use Modules\Donation\Exceptions\DuplicateDonorEmailException;
use Modules\Donation\Models\Donor;

class DonorRegistrationService
{
    /**
     * @param  list<string>  $tags
     */
    public function register(
        int $tenantId,
        string $name,
        ?string $email = null,
        ?string $phone = null,
        bool $isAnonymous = false,
        ?int $userId = null,
        array $tags = [],
    ): Donor {
        $normalizedEmail = $email !== null ? strtolower(trim($email)) : null;

        if ($normalizedEmail !== null && $normalizedEmail !== '' && ! $isAnonymous) {
            if ($this->emailExists($tenantId, $normalizedEmail)) {
                throw new DuplicateDonorEmailException($normalizedEmail);
            }
        }

        return Donor::withoutTenantScope()->create([
            'tenant_id' => $tenantId,
            'user_id' => $userId,
            'name' => trim($name),
            'email' => $isAnonymous ? null : $normalizedEmail,
            'phone' => $phone !== null ? trim($phone) : null,
            'is_anonymous' => $isAnonymous,
            'tags' => $tags,
        ]);
    }

    protected function emailExists(int $tenantId, string $email): bool
    {
        return Donor::withoutTenantScope()
            ->where('tenant_id', $tenantId)
            ->where('is_anonymous', false)
            ->whereRaw('LOWER(email) = ?', [$email])
            ->exists();
    }
}
