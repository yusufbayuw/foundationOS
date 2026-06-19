<?php

declare(strict_types=1);

namespace Modules\Library\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Library\Models\LibraryCollectionType;

class LibraryCollectionTypePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:LibraryCollectionType');
    }

    public function view(AuthUser $authUser, LibraryCollectionType $libraryCollectionType): bool
    {
        return $authUser->can('View:LibraryCollectionType');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:LibraryCollectionType');
    }

    public function update(AuthUser $authUser, LibraryCollectionType $libraryCollectionType): bool
    {
        return $authUser->can('Update:LibraryCollectionType');
    }

    public function delete(AuthUser $authUser, LibraryCollectionType $libraryCollectionType): bool
    {
        return $authUser->can('Delete:LibraryCollectionType');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:LibraryCollectionType');
    }

    public function restore(AuthUser $authUser, LibraryCollectionType $libraryCollectionType): bool
    {
        return $authUser->can('Restore:LibraryCollectionType');
    }

    public function forceDelete(AuthUser $authUser, LibraryCollectionType $libraryCollectionType): bool
    {
        return $authUser->can('ForceDelete:LibraryCollectionType');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:LibraryCollectionType');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:LibraryCollectionType');
    }

    public function replicate(AuthUser $authUser, LibraryCollectionType $libraryCollectionType): bool
    {
        return $authUser->can('Replicate:LibraryCollectionType');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:LibraryCollectionType');
    }
}
