<?php

namespace Modules\Core\Models\Concerns;

use App\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\Tenant;

/**
 * Trait for models that belong to a tenant.
 *
 * Provides:
 * - Automatic tenant_id scoping via TenantScope (when a tenant context is bound)
 * - tenant() BelongsTo relationship
 * - Auto-fill tenant_id on creating event
 * - withoutTenantScope() / allTenants() helpers for cross-tenant CLI work
 *
 * Tenant resolution priority is implemented in App\Support\CurrentTenant.
 *
 * @property int|null $tenant_id
 */
trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        static::addGlobalScope(new TenantScope);

        static::creating(function (Model $model) {
            if (empty($model->getAttribute('tenant_id'))) {
                $tenantId = TenantScope::resolveTenantId();
                if ($tenantId !== null) {
                    $model->setAttribute('tenant_id', $tenantId);
                }
            }
        });
    }

    /**
     * @return BelongsTo<Tenant, $this>
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Query builder without the tenant scope — for cross-tenant CLI/seeders.
     *
     * @return Builder<static>
     */
    public static function withoutTenantScope(): Builder
    {
        return static::query()->withoutGlobalScope(TenantScope::class);
    }

    /**
     * Alias matching common multi-tenancy package naming.
     *
     * @return Builder<static>
     */
    public static function allTenants(): Builder
    {
        return static::withoutTenantScope();
    }
}
