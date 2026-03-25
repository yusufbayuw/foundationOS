<?php

declare(strict_types=1);

namespace Modules\Core\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Core\Models\TenantModule;
use Illuminate\Auth\Access\HandlesAuthorization;

class TenantModulePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:TenantModule');
    }

    public function view(AuthUser $authUser, TenantModule $tenantModule): bool
    {
        return $authUser->can('View:TenantModule');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:TenantModule');
    }

    public function update(AuthUser $authUser, TenantModule $tenantModule): bool
    {
        return $authUser->can('Update:TenantModule');
    }

    public function delete(AuthUser $authUser, TenantModule $tenantModule): bool
    {
        return $authUser->can('Delete:TenantModule');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:TenantModule');
    }

    public function restore(AuthUser $authUser, TenantModule $tenantModule): bool
    {
        return $authUser->can('Restore:TenantModule');
    }

    public function forceDelete(AuthUser $authUser, TenantModule $tenantModule): bool
    {
        return $authUser->can('ForceDelete:TenantModule');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:TenantModule');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:TenantModule');
    }

    public function replicate(AuthUser $authUser, TenantModule $tenantModule): bool
    {
        return $authUser->can('Replicate:TenantModule');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:TenantModule');
    }

}