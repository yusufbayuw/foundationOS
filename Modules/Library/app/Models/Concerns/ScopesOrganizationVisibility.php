<?php

namespace Modules\Library\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Modules\Core\Models\Organization;
use Modules\Library\Support\Queries\OrganizationVisibilityQuery;

trait ScopesOrganizationVisibility
{
    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeVisibleForOrganization(Builder $query, ?Organization $organization, string $column = 'organization_id'): Builder
    {
        OrganizationVisibilityQuery::applyTenantWideOrOrganization($query, $organization, $column);

        return $query;
    }
}
