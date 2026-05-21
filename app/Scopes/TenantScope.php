<?php

namespace App\Scopes;

use App\Support\CurrentTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Global scope that restricts queries to the active tenant's records.
 *
 * No-op when no tenant context is bound — allows console/seeder cross-tenant work.
 */
class TenantScope implements Scope
{
    public const NAME = 'tenant';

    public function apply(Builder $builder, Model $model): void
    {
        $tenantId = $this->resolveTenantId();

        if ($tenantId === null) {
            return;
        }

        $builder->where($model->qualifyColumn('tenant_id'), $tenantId);
    }

    public static function resolveTenantId(): int|string|null
    {
        if (! app()->bound(CurrentTenant::class)) {
            return null;
        }

        return app(CurrentTenant::class)->id();
    }
}
