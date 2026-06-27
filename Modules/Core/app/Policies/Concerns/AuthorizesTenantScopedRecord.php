<?php

namespace Modules\Core\Policies\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Core\Support\Tenancy\CurrentTenant;

trait AuthorizesTenantScopedRecord
{
    protected function belongsToActiveTenant(AuthUser $authUser, Model $record): bool
    {
        if (! $this->recordHasTenantId($record)) {
            return true;
        }

        if ($authUser instanceof User && $authUser->isGlobalSuperAdmin()) {
            return true;
        }

        $recordTenantId = (int) $record->getAttribute('tenant_id');
        $activeTenantId = $this->resolveActiveTenantId();

        if ($activeTenantId !== null) {
            return $recordTenantId === (int) $activeTenantId;
        }

        if ($authUser instanceof User) {
            return $authUser->canAccessTenant(
                Tenant::query()->find($recordTenantId) ?? new Tenant(['id' => $recordTenantId])
            );
        }

        return false;
    }

    protected function recordHasTenantId(Model $record): bool
    {
        return array_key_exists('tenant_id', $record->getAttributes())
            || $record->getAttribute('tenant_id') !== null;
    }

    protected function resolveActiveTenantId(): int|string|null
    {
        if (app()->bound(CurrentTenant::class)) {
            return app(CurrentTenant::class)->id();
        }

        return null;
    }
}
