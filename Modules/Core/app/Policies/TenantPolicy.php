<?php

declare(strict_types=1);

namespace Modules\Core\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;

class TenantPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $authUser): bool
    {
        return $authUser->can('ViewAny:Tenant');
    }

    public function view(User $authUser, Tenant $tenant): bool
    {
        return $authUser->can('View:Tenant');
    }

    public function create(User $authUser): bool
    {
        if ($authUser->can('Create:Tenant')) {
            return true;
        }

        return $authUser->hasVerifiedEmail()
            && ! $authUser->userTenantRoles()->exists()
            && ! $authUser->createdTenants()->exists();
    }

    public function update(User $authUser, Tenant $tenant): bool
    {
        return $authUser->can('Update:Tenant');
    }

    public function delete(User $authUser, Tenant $tenant): bool
    {
        return $authUser->can('Delete:Tenant');
    }

    public function deleteAny(User $authUser): bool
    {
        return $authUser->can('DeleteAny:Tenant');
    }

    public function restore(User $authUser, Tenant $tenant): bool
    {
        return $authUser->can('Restore:Tenant');
    }

    public function forceDelete(User $authUser, Tenant $tenant): bool
    {
        return $authUser->can('ForceDelete:Tenant');
    }

    public function forceDeleteAny(User $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Tenant');
    }

    public function restoreAny(User $authUser): bool
    {
        return $authUser->can('RestoreAny:Tenant');
    }

    public function replicate(User $authUser, Tenant $tenant): bool
    {
        return $authUser->can('Replicate:Tenant');
    }

    public function reorder(User $authUser): bool
    {
        return $authUser->can('Reorder:Tenant');
    }
}
