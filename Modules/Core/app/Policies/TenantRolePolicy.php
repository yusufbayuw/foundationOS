<?php

declare(strict_types=1);

namespace Modules\Core\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Core\Models\TenantRole;

class TenantRolePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:TenantRole');
    }

    public function view(AuthUser $authUser, TenantRole $tenantRole): bool
    {
        return $authUser->can('View:TenantRole');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:TenantRole');
    }

    public function update(AuthUser $authUser, TenantRole $tenantRole): bool
    {
        return $authUser->can('Update:TenantRole');
    }

    public function delete(AuthUser $authUser, TenantRole $tenantRole): bool
    {
        return $authUser->can('Delete:TenantRole');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:TenantRole');
    }

    public function restore(AuthUser $authUser, TenantRole $tenantRole): bool
    {
        return $authUser->can('Restore:TenantRole');
    }

    public function forceDelete(AuthUser $authUser, TenantRole $tenantRole): bool
    {
        return $authUser->can('ForceDelete:TenantRole');
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
        return $authUser->can('Replicate:TenantRole');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:TenantRole');
    }
}
