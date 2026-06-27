<?php

namespace Modules\Legal\Services;

use Modules\Legal\Exceptions\DuplicateLegalDocumentCodeException;
use Modules\Legal\Models\LegalDocument;

class LegalDocumentRegistrationService
{
    /**
     * @param  array<string, mixed>  $meta
     */
    public function register(
        int $tenantId,
        ?int $organizationId,
        string $code,
        string $name,
        ?string $documentType = null,
        ?string $description = null,
        array $meta = [],
        string $status = 'active',
    ): LegalDocument {
        $normalizedCode = strtoupper(trim($code));

        if ($normalizedCode === '') {
            throw new \InvalidArgumentException('Legal document code cannot be empty.');
        }

        if ($this->codeExists($tenantId, $organizationId, $normalizedCode)) {
            throw new DuplicateLegalDocumentCodeException($normalizedCode);
        }

        return LegalDocument::withoutTenantScope()->create([
            'tenant_id' => $tenantId,
            'organization_id' => $organizationId,
            'code' => $normalizedCode,
            'name' => trim($name),
            'document_type' => $documentType,
            'status' => $status,
            'description' => $description,
            'meta' => $meta,
        ]);
    }

    protected function codeExists(int $tenantId, ?int $organizationId, string $code): bool
    {
        return LegalDocument::withoutTenantScope()
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
