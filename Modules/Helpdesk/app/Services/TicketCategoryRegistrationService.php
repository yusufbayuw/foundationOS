<?php

namespace Modules\Helpdesk\Services;

use Modules\Helpdesk\Exceptions\DuplicateTicketCategoryCodeException;
use Modules\Helpdesk\Models\TicketCategory;

class TicketCategoryRegistrationService
{
    /**
     * @param  array<string, mixed>  $meta
     */
    public function register(
        int $tenantId,
        ?int $organizationId,
        string $code,
        string $name,
        int $responseHours = 4,
        int $resolutionHours = 24,
        ?int $defaultAssigneeUserId = null,
        ?string $description = null,
        array $meta = [],
        string $status = 'active',
    ): TicketCategory {
        $normalizedCode = strtoupper(trim($code));

        if ($normalizedCode === '') {
            throw new \InvalidArgumentException('Ticket category code cannot be empty.');
        }

        if ($this->codeExists($tenantId, $organizationId, $normalizedCode)) {
            throw new DuplicateTicketCategoryCodeException($normalizedCode);
        }

        return TicketCategory::withoutTenantScope()->create([
            'tenant_id' => $tenantId,
            'organization_id' => $organizationId,
            'code' => $normalizedCode,
            'name' => trim($name),
            'status' => $status,
            'response_hours' => $responseHours,
            'resolution_hours' => $resolutionHours,
            'default_assignee_user_id' => $defaultAssigneeUserId,
            'description' => $description,
            'meta' => $meta,
        ]);
    }

    protected function codeExists(int $tenantId, ?int $organizationId, string $code): bool
    {
        return TicketCategory::withoutTenantScope()
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
