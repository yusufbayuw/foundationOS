<?php

declare(strict_types=1);

namespace Modules\Library\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Library\Models\LibrarySerial;

class LibrarySerialPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:LibrarySerial');
    }

    public function view(AuthUser $authUser, LibrarySerial $librarySerial): bool
    {
        return $authUser->can('View:LibrarySerial');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:LibrarySerial');
    }

    public function update(AuthUser $authUser, LibrarySerial $librarySerial): bool
    {
        return $authUser->can('Update:LibrarySerial');
    }

    public function delete(AuthUser $authUser, LibrarySerial $librarySerial): bool
    {
        return $authUser->can('Delete:LibrarySerial');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:LibrarySerial');
    }

    public function restore(AuthUser $authUser, LibrarySerial $librarySerial): bool
    {
        return $authUser->can('Restore:LibrarySerial');
    }

    public function forceDelete(AuthUser $authUser, LibrarySerial $librarySerial): bool
    {
        return $authUser->can('ForceDelete:LibrarySerial');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:LibrarySerial');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:LibrarySerial');
    }

    public function replicate(AuthUser $authUser, LibrarySerial $librarySerial): bool
    {
        return $authUser->can('Replicate:LibrarySerial');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:LibrarySerial');
    }
}
