<?php

namespace Modules\Core\Models\Concerns;

use App\Scopes\TenantScope;
use Illuminate\Auth\Access\AuthorizationException;
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

        static::saving(function (Model $model): void {
            $tenantId = TenantScope::resolveTenantId();

            // Global CLI/system provisioning remains an explicit separate pathway;
            // when a tenant IS bound, a forged tenant_id must never be accepted.
            if ($tenantId === null) {
                return;
            }

            if ($model->getAttribute('tenant_id') === null) {
                $model->setAttribute('tenant_id', $tenantId);
            }

            if ((string) $model->getAttribute('tenant_id') !== (string) $tenantId
                || ($model->exists && (string) $model->getRawOriginal('tenant_id') !== (string) $tenantId)) {
                throw new AuthorizationException('Cross-tenant model write is not allowed.');
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
