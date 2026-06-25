<?php

namespace Modules\Library\Services\Opac;

use Illuminate\Database\Eloquent\Builder;
use Modules\Core\Models\Organization;
use Modules\Core\Models\Tenant;
use Modules\Library\Models\Book;

class OpacContextResolver
{
    public function resolveTenant(string $tenant): ?Tenant
    {
        return Tenant::query()
            ->where('code', $tenant)
            ->orWhere('id', $tenant)
            ->first();
    }

    public function resolveOrganization(Tenant $tenant, string $organization): ?Organization
    {
        return Organization::query()
            ->where('tenant_id', $tenant->id)
            ->where(function (Builder $query) use ($organization): void {
                $query->where('code', $organization)
                    ->orWhere('id', $organization);
            })
            ->first();
    }

    public function bookVisibleForOrganization(Book $book, Organization $organization): bool
    {
        return $book->organization_id === null || (int) $book->organization_id === (int) $organization->id;
    }
}
