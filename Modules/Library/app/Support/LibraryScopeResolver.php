<?php

namespace Modules\Library\Support;

use App\Support\TypedValue;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;

class LibraryScopeResolver
{
    public function resolveTenantId(?int $tenantId = null): ?int
    {
        if ($tenantId !== null && $tenantId > 0) {
            return $tenantId;
        }

        $tenant = Filament::getTenant();

        if ($tenant instanceof Tenant) {
            return TypedValue::int($tenant->getKey());
        }

        return null;
    }

    /**
     * @template TModel of \Illuminate\Database\Eloquent\Model
     *
     * @param  Builder<TModel>  $query
     * @return Builder<TModel>
     */
    public function apply(Builder $query, ?User $user = null, ?int $tenantId = null): Builder
    {
        $tenantId = $this->resolveTenantId($tenantId);

        if ($tenantId === null) {
            return $query->whereRaw('1 = 0');
        }

        $query->where($query->getModel()->qualifyColumn('tenant_id'), $tenantId);

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
        if (! isset($record->tenant_id) || TypedValue::int($record->tenant_id) <= 0) {
            return false;
        }

        if ($user === null) {
            return false;
        }

        if ($user->isGlobalSuperAdmin()) {
            return true;
        }

        $assignmentQuery = $user->userTenantRoles()->where('tenant_id', TypedValue::int($record->tenant_id));

        if (! isset($record->organization_id)) {
            return $assignmentQuery->exists();
        }

        return $assignmentQuery
            ->where(function (Builder $builder) use ($record): void {
                $builder->whereNull('organization_id')
                    ->orWhere('organization_id', TypedValue::int($record->organization_id));
            })
            ->exists();
    }

    /** @param Builder<covariant Model> $query */
    protected function hasOrganizationColumn(Builder $query): bool
    {
        return in_array('organization_id', $query->getModel()->getFillable(), true);
    }
}
