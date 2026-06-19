<?php

declare(strict_types=1);

namespace Modules\Library\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Library\Models\LibraryItemStatus;

class LibraryItemStatusPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:LibraryItemStatus');
    }

    public function view(AuthUser $authUser, LibraryItemStatus $libraryItemStatus): bool
    {
        return $authUser->can('View:LibraryItemStatus');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:LibraryItemStatus');
    }

    public function update(AuthUser $authUser, LibraryItemStatus $libraryItemStatus): bool
    {
        return $authUser->can('Update:LibraryItemStatus');
    }

    public function delete(AuthUser $authUser, LibraryItemStatus $libraryItemStatus): bool
    {
        return $authUser->can('Delete:LibraryItemStatus');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:LibraryItemStatus');
    }

    public function restore(AuthUser $authUser, LibraryItemStatus $libraryItemStatus): bool
    {
        return $authUser->can('Restore:LibraryItemStatus');
    }

    public function forceDelete(AuthUser $authUser, LibraryItemStatus $libraryItemStatus): bool
    {
        return $authUser->can('ForceDelete:LibraryItemStatus');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:LibraryItemStatus');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:LibraryItemStatus');
    }

    public function replicate(AuthUser $authUser, LibraryItemStatus $libraryItemStatus): bool
    {
        return $authUser->can('Replicate:LibraryItemStatus');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:LibraryItemStatus');
    }
}
