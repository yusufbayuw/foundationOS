<?php

declare(strict_types=1);

namespace Modules\Library\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Library\Models\LibraryAuthor;

class LibraryAuthorPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:LibraryAuthor');
    }

    public function view(AuthUser $authUser, LibraryAuthor $libraryAuthor): bool
    {
        return $authUser->can('View:LibraryAuthor');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:LibraryAuthor');
    }

    public function update(AuthUser $authUser, LibraryAuthor $libraryAuthor): bool
    {
        return $authUser->can('Update:LibraryAuthor');
    }

    public function delete(AuthUser $authUser, LibraryAuthor $libraryAuthor): bool
    {
        return $authUser->can('Delete:LibraryAuthor');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:LibraryAuthor');
    }

    public function restore(AuthUser $authUser, LibraryAuthor $libraryAuthor): bool
    {
        return $authUser->can('Restore:LibraryAuthor');
    }

    public function forceDelete(AuthUser $authUser, LibraryAuthor $libraryAuthor): bool
    {
        return $authUser->can('ForceDelete:LibraryAuthor');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:LibraryAuthor');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:LibraryAuthor');
    }

    public function replicate(AuthUser $authUser, LibraryAuthor $libraryAuthor): bool
    {
        return $authUser->can('Replicate:LibraryAuthor');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:LibraryAuthor');
    }
}
