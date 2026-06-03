<?php

namespace App\Scopes;

use App\Exceptions\MissingTenantContextException;
use App\Support\CurrentTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Global scope that restricts queries to the active tenant's records.
 *
 * When tenancy.scope_fail_closed is enabled, HTTP requests and PHPUnit runs
 * without tenant context throw MissingTenantContextException. Real Artisan/queue
 * console (non-test) stays fail-open for cross-tenant commands.
 */
class TenantScope implements Scope
{
    public const NAME = 'tenant';

    public function apply(Builder $builder, Model $model): void
    {
        $tenantId = $this->resolveTenantId();

        if ($tenantId === null) {
            if ($this->shouldFailClosed($model)) {
                throw new MissingTenantContextException($model::class);
            }

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

    protected function shouldFailClosed(Model $model): bool
    {
        if (! config('tenancy.scope_fail_closed', false)) {
            return false;
        }

        if (! app()->runningInConsole()) {
            return true;
        }

        return app()->runningUnitTests();
    }
}
