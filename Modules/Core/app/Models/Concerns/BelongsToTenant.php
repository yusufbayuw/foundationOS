<?php

namespace Modules\Core\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\Tenant;
use Modules\Core\Scopes\TenantScope;

/**
 * Trait for models that belong to a tenant.
 *
 * Provides:
 * - Automatic tenant_id scoping via TenantScope (when a tenant context is bound)
 * - tenant() BelongsTo relationship
 * - Auto-fill tenant_id on creating event
 * - withoutTenantScope() / allTenants() helpers for cross-tenant CLI work
 *
 * Tenant resolution is implemented in Modules\Core\Support\Tenancy\CurrentTenant.
 */
trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        static::addGlobalScope(new TenantScope);

        static::creating(function (Model $model) {
            if (empty($model->tenant_id)) {
                $tenantId = TenantScope::resolveTenantId();
                if ($tenantId !== null) {
                    $model->tenant_id = $tenantId;
                }
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Query builder without the tenant scope — for cross-tenant CLI/seeders.
     */
    public static function withoutTenantScope()
    {
        return static::query()->withoutGlobalScope(TenantScope::class);
    }

    /**
     * Alias matching common multi-tenancy package naming.
     */
    public static function allTenants()
    {
        return static::withoutTenantScope();
    }
}
