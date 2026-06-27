<?php

namespace Modules\Core\Providers;

use App\Support\CurrentTenant;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use Modules\Core\Models\Tenant;
use Modules\Core\Services\TenantDomain\TenantDomainAggregateService;
use Modules\Core\Services\TenantDomain\TenantDomainRelationBridge;

class TenancyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! $this->app->bound(CurrentTenant::class)) {
            $this->app->singleton(CurrentTenant::class);
            $this->app->alias(
                CurrentTenant::class,
                \Modules\Core\Support\Tenancy\CurrentTenant::class,
            );
        }

        $this->app->singleton(TenantDomainAggregateService::class);
        $this->app->singleton(TenantDomainRelationBridge::class);
    }

    public function boot(): void
    {
        $bridge = $this->app->make(TenantDomainRelationBridge::class);

        foreach ($bridge->relationNames() as $relationName) {
            Tenant::resolveRelationUsing(
                $relationName,
                fn (Tenant $tenant): Relation => $bridge->buildRelation($tenant, $relationName),
            );
        }
    }
}
