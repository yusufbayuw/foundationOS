<?php

namespace Modules\Library\Support\Queries;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Models\Organization;

class OrganizationVisibilityQuery
{
    /**
     * @param  Builder<Model>  $query
     */
    public static function applyTenantWideOrOrganization(Builder $query, ?Organization $organization, string $column = 'organization_id'): void
    {
        if ($organization === null) {
            return;
        }

        $query->where(function (Builder $builder) use ($organization, $column): void {
            $builder->whereNull($column)
                ->orWhere($column, $organization->id);
        });
    }
}
