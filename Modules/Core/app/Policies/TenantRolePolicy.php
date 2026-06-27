<?php

declare(strict_types=1);

namespace Modules\Core\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Core\Models\TenantRole;
use Modules\Core\Policies\Concerns\AuthorizesTenantScopedRecord;

class TenantRolePolicy
{
    use AuthorizesTenantScopedRecord;
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:TenantRole');
    }

    public function view(AuthUser $authUser, TenantRole $tenantRole): bool
    {
        return $authUser->can('View:TenantRole')
            && $this->belongsToActiveTenant($authUser, $tenantRole);
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:TenantRole');
    }

    public function update(AuthUser $authUser, TenantRole $tenantRole): bool
    {
        return $authUser->can('Update:TenantRole')
            && $this->belongsToActiveTenant($authUser, $tenantRole);
    }

    public function delete(AuthUser $authUser, TenantRole $tenantRole): bool
    {
        return $authUser->can('Delete:TenantRole')
            && $this->belongsToActiveTenant($authUser, $tenantRole);
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:TenantRole');
    }

    public function restore(AuthUser $authUser, TenantRole $tenantRole): bool
    {
        return $authUser->can('Restore:TenantRole')
            && $this->belongsToActiveTenant($authUser, $tenantRole);
    }

    public function forceDelete(AuthUser $authUser, TenantRole $tenantRole): bool
    {
        return $authUser->can('ForceDelete:TenantRole')
            && $this->belongsToActiveTenant($authUser, $tenantRole);
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:TenantRole');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:TenantRole');
    }

    public function replicate(AuthUser $authUser, TenantRole $tenantRole): bool
    {
        return $authUser->can('Replicate:TenantRole')
            && $this->belongsToActiveTenant($authUser, $tenantRole);
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:TenantRole');
    }
}
