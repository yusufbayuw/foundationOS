<?php

declare(strict_types=1);

namespace Modules\Cafeteria\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Cafeteria\Models\CafeteriaTenant;

class CafeteriaTenantPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CafeteriaTenant');
    }

    public function view(AuthUser $authUser, CafeteriaTenant $cafeteriaTenant): bool
    {
        return $authUser->can('View:CafeteriaTenant');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CafeteriaTenant');
    }

    public function update(AuthUser $authUser, CafeteriaTenant $cafeteriaTenant): bool
    {
        return $authUser->can('Update:CafeteriaTenant');
    }

    public function delete(AuthUser $authUser, CafeteriaTenant $cafeteriaTenant): bool
    {
        return $authUser->can('Delete:CafeteriaTenant');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:CafeteriaTenant');
    }

    public function restore(AuthUser $authUser, CafeteriaTenant $cafeteriaTenant): bool
    {
        return $authUser->can('Restore:CafeteriaTenant');
    }

    public function forceDelete(AuthUser $authUser, CafeteriaTenant $cafeteriaTenant): bool
    {
        return $authUser->can('ForceDelete:CafeteriaTenant');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CafeteriaTenant');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CafeteriaTenant');
    }

    public function replicate(AuthUser $authUser, CafeteriaTenant $cafeteriaTenant): bool
    {
        return $authUser->can('Replicate:CafeteriaTenant');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CafeteriaTenant');
    }
}
