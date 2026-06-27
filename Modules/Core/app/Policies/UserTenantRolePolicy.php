<?php

declare(strict_types=1);

namespace Modules\Core\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Core\Models\UserTenantRole;
use Modules\Core\Policies\Concerns\AuthorizesTenantScopedRecord;

class UserTenantRolePolicy
{
    use AuthorizesTenantScopedRecord;
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:UserTenantRole');
    }

    public function view(AuthUser $authUser, UserTenantRole $userTenantRole): bool
    {
        return $authUser->can('View:UserTenantRole')
            && $this->belongsToActiveTenant($authUser, $userTenantRole);
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:UserTenantRole');
    }

    public function update(AuthUser $authUser, UserTenantRole $userTenantRole): bool
    {
        return $authUser->can('Update:UserTenantRole')
            && $this->belongsToActiveTenant($authUser, $userTenantRole);
    }

    public function delete(AuthUser $authUser, UserTenantRole $userTenantRole): bool
    {
        return $authUser->can('Delete:UserTenantRole')
            && $this->belongsToActiveTenant($authUser, $userTenantRole);
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:UserTenantRole');
    }

    public function restore(AuthUser $authUser, UserTenantRole $userTenantRole): bool
    {
        return $authUser->can('Restore:UserTenantRole')
            && $this->belongsToActiveTenant($authUser, $userTenantRole);
    }

    public function forceDelete(AuthUser $authUser, UserTenantRole $userTenantRole): bool
    {
        return $authUser->can('ForceDelete:UserTenantRole')
            && $this->belongsToActiveTenant($authUser, $userTenantRole);
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:UserTenantRole');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:UserTenantRole');
    }

    public function replicate(AuthUser $authUser, UserTenantRole $userTenantRole): bool
    {
        return $authUser->can('Replicate:UserTenantRole')
            && $this->belongsToActiveTenant($authUser, $userTenantRole);
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:UserTenantRole');
    }
}
