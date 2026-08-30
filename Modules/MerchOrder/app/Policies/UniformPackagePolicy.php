<?php

declare(strict_types=1);

namespace Modules\MerchOrder\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\MerchOrder\Models\UniformPackage;

class UniformPackagePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:UniformPackage');
    }

    public function view(AuthUser $authUser, UniformPackage $uniformPackage): bool
    {
        return $authUser->can('View:UniformPackage');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:UniformPackage');
    }

    public function update(AuthUser $authUser, UniformPackage $uniformPackage): bool
    {
        return $authUser->can('Update:UniformPackage');
    }

    public function delete(AuthUser $authUser, UniformPackage $uniformPackage): bool
    {
        return $authUser->can('Delete:UniformPackage');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:UniformPackage');
    }

    public function restore(AuthUser $authUser, UniformPackage $uniformPackage): bool
    {
        return $authUser->can('Restore:UniformPackage');
    }

    public function forceDelete(AuthUser $authUser, UniformPackage $uniformPackage): bool
    {
        return $authUser->can('ForceDelete:UniformPackage');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:UniformPackage');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:UniformPackage');
    }

    public function replicate(AuthUser $authUser, UniformPackage $uniformPackage): bool
    {
        return $authUser->can('Replicate:UniformPackage');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:UniformPackage');
    }
}
