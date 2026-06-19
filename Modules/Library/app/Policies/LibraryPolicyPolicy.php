<?php

declare(strict_types=1);

namespace Modules\Library\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Library\Models\LibraryPolicy;

class LibraryPolicyPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:LibraryPolicy');
    }

    public function view(AuthUser $authUser, LibraryPolicy $libraryPolicy): bool
    {
        return $authUser->can('View:LibraryPolicy');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:LibraryPolicy');
    }

    public function update(AuthUser $authUser, LibraryPolicy $libraryPolicy): bool
    {
        return $authUser->can('Update:LibraryPolicy');
    }

    public function delete(AuthUser $authUser, LibraryPolicy $libraryPolicy): bool
    {
        return $authUser->can('Delete:LibraryPolicy');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:LibraryPolicy');
    }

    public function restore(AuthUser $authUser, LibraryPolicy $libraryPolicy): bool
    {
        return $authUser->can('Restore:LibraryPolicy');
    }

    public function forceDelete(AuthUser $authUser, LibraryPolicy $libraryPolicy): bool
    {
        return $authUser->can('ForceDelete:LibraryPolicy');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:LibraryPolicy');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:LibraryPolicy');
    }

    public function replicate(AuthUser $authUser, LibraryPolicy $libraryPolicy): bool
    {
        return $authUser->can('Replicate:LibraryPolicy');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:LibraryPolicy');
    }
}
