<?php

namespace Modules\Core\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\Tenant;

/**
 * Trait for models that belong to a tenant.
 *
 * Provides:
 * - Automatic tenant_id scoping via global scope (when Filament tenant is active)
 * - tenant() BelongsTo relationship
 * - Auto-fill tenant_id on creating event
 *
 * Usage:
 *   use BelongsToTenant;
 *
 * The global scope only applies when a Filament tenant is resolved,
 * so CLI commands and queue jobs without tenant context remain unrestricted.
 */
trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            $tenantId = static::resolveCurrentTenantId();

            if ($tenantId !== null) {
                $builder->where($builder->getModel()->qualifyColumn('tenant_id'), $tenantId);
            }
        });

        static::creating(function (Model $model) {
            if (empty($model->tenant_id)) {
                $tenantId = static::resolveCurrentTenantId();
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
     * Resolve the current tenant ID from Filament context.
     *
     * Returns null when no tenant context exists (CLI, queue, non-panel request).
     */
    protected static function resolveCurrentTenantId(): ?int
    {
        if (! class_exists(\Filament\Facades\Filament::class)) {
            return null;
        }

        try {
            $tenant = \Filament\Facades\Filament::getTenant();
            return $tenant?->getKey();
        } catch (\Throwable) {
            return null;
        }
    }
}
