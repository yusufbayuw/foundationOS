<?php

namespace Modules\Core\Scopes;

use App\Http\Middleware\MarkHttpTenancyContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Modules\Core\Exceptions\MissingTenantContextException;
use Modules\Core\Support\Tenancy\CurrentTenant;

/**
 * Global scope that restricts queries to the active tenant's records.
 *
 * HTTP requests always fail-closed when tenant context is missing.
 * Artisan/queue console stays fail-open unless running PHPUnit with
 * tenancy.scope_fail_closed enabled, so seeders and cross-tenant commands
 * can use withoutTenantScope() explicitly.
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
        if ($this->isHttpContext()) {
            return true;
        }

        if (! config('tenancy.scope_fail_closed', false)) {
            return false;
        }

        return app()->runningUnitTests();
    }

    protected function isHttpContext(): bool
    {
        if (! app()->runningInConsole()) {
            return true;
        }

        if (! app()->bound('request')) {
            return false;
        }

        return request()->attributes->get(MarkHttpTenancyContext::ATTRIBUTE) === true;
    }
}
