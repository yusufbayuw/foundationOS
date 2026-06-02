<?php

namespace Modules\Cms\Services;

use Modules\Cms\Exceptions\DuplicateCmsSiteCodeException;
use Modules\Cms\Models\Site;

class CmsSiteRegistrationService
{
    public function register(
        int $tenantId,
        string $code,
        string $name,
        ?string $domain = null,
        string $defaultLocale = 'id',
    ): Site {
        $normalizedCode = strtolower(trim($code));

        if ($normalizedCode === '') {
            throw new \InvalidArgumentException('Site code cannot be empty.');
        }

        if (Site::withoutTenantScope()
            ->where('tenant_id', $tenantId)
            ->where('code', $normalizedCode)
            ->exists()) {
            throw new DuplicateCmsSiteCodeException($normalizedCode);
        }

        return Site::withoutTenantScope()->create([
            'tenant_id' => $tenantId,
            'code' => $normalizedCode,
            'name' => trim($name),
            'domain' => $domain,
            'default_locale' => $defaultLocale,
            'is_active' => true,
        ]);
    }
}
