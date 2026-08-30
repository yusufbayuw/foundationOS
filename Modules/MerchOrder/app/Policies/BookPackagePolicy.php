<?php

declare(strict_types=1);

namespace Modules\MerchOrder\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\MerchOrder\Models\BookPackage;

class BookPackagePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:BookPackage');
    }

    public function view(AuthUser $authUser, BookPackage $bookPackage): bool
    {
        return $authUser->can('View:BookPackage');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:BookPackage');
    }

    public function update(AuthUser $authUser, BookPackage $bookPackage): bool
    {
        return $authUser->can('Update:BookPackage');
    }

    public function delete(AuthUser $authUser, BookPackage $bookPackage): bool
    {
        return $authUser->can('Delete:BookPackage');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:BookPackage');
    }

    public function restore(AuthUser $authUser, BookPackage $bookPackage): bool
    {
        return $authUser->can('Restore:BookPackage');
    }

    public function forceDelete(AuthUser $authUser, BookPackage $bookPackage): bool
    {
        return $authUser->can('ForceDelete:BookPackage');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:BookPackage');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:BookPackage');
    }

    public function replicate(AuthUser $authUser, BookPackage $bookPackage): bool
    {
        return $authUser->can('Replicate:BookPackage');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:BookPackage');
    }
}
