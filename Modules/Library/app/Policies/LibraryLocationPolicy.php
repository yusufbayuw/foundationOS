<?php

declare(strict_types=1);

namespace Modules\Library\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Library\Models\LibraryLocation;

class LibraryLocationPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:LibraryLocation');
    }

    public function view(AuthUser $authUser, LibraryLocation $libraryLocation): bool
    {
        return $authUser->can('View:LibraryLocation');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:LibraryLocation');
    }

    public function update(AuthUser $authUser, LibraryLocation $libraryLocation): bool
    {
        return $authUser->can('Update:LibraryLocation');
    }

    public function delete(AuthUser $authUser, LibraryLocation $libraryLocation): bool
    {
        return $authUser->can('Delete:LibraryLocation');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:LibraryLocation');
    }

    public function restore(AuthUser $authUser, LibraryLocation $libraryLocation): bool
    {
        return $authUser->can('Restore:LibraryLocation');
    }

    public function forceDelete(AuthUser $authUser, LibraryLocation $libraryLocation): bool
    {
        return $authUser->can('ForceDelete:LibraryLocation');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:LibraryLocation');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:LibraryLocation');
    }

    public function replicate(AuthUser $authUser, LibraryLocation $libraryLocation): bool
    {
        return $authUser->can('Replicate:LibraryLocation');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:LibraryLocation');
    }
}
