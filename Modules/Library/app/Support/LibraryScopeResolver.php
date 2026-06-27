<?php

namespace Modules\Library\Support;

use Illuminate\Database\Eloquent\Builder;
use Modules\Core\Models\User;

class LibraryScopeResolver
{
    public function resolveTenantId(?int $tenantId = null): ?int
    {
        if ($tenantId !== null && $tenantId > 0) {
            return $tenantId;
        }

        $tenantId = current_tenant_id();

        if ($tenantId === null) {
            return null;
        }

        return (int) $tenantId;
    }

    /**
     * Library data is always tenant-owned, while organization may be tenant-wide.
     */
    public function apply(Builder $query, ?User $user = null, ?int $tenantId = null): Builder
    {
        $tenantId = $this->resolveTenantId($tenantId);

        if ($tenantId === null) {
            return $query->whereRaw('1 = 0');
        }

        $query->where($query->getModel()->getTable().'.tenant_id', $tenantId);

        if (! $this->hasOrganizationColumn($query) || $user === null || $user->isGlobalSuperAdmin()) {
            return $query;
        }

        $organizationIds = $user->userTenantRoles()
            ->where('tenant_id', $tenantId)
            ->whereNotNull('organization_id')
            ->pluck('organization_id')
            ->filter()
            ->unique()
            ->values()
            ->all();

        if ($organizationIds === []) {
            return $query;
        }

        return $query->where(function (Builder $builder) use ($organizationIds): void {
            $builder->whereNull($builder->getModel()->getTable().'.organization_id')
                ->orWhereIn($builder->getModel()->getTable().'.organization_id', $organizationIds);
        });
    }

    public function isVisibleToUser(object $record, ?User $user = null): bool
    {
        if (! isset($record->tenant_id) || (int) $record->tenant_id <= 0) {
            return false;
        }

        if ($user === null) {
            return false;
        }

        if ($user->isGlobalSuperAdmin()) {
            return true;
        }

        $assignmentQuery = $user->userTenantRoles()->where('tenant_id', (int) $record->tenant_id);

        if (! isset($record->organization_id) || $record->organization_id === null) {
            return $assignmentQuery->exists();
        }

        return $assignmentQuery
            ->where(function (Builder $builder) use ($record): void {
                $builder->whereNull('organization_id')
                    ->orWhere('organization_id', (int) $record->organization_id);
            })
            ->exists();
    }

    protected function hasOrganizationColumn(Builder $query): bool
    {
        return in_array('organization_id', $query->getModel()->getFillable(), true);
    }
}
