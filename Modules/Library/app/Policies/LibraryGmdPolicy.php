<?php

declare(strict_types=1);

namespace Modules\Library\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Library\Models\LibraryGmd;

class LibraryGmdPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:LibraryGmd');
    }

    public function view(AuthUser $authUser, LibraryGmd $libraryGmd): bool
    {
        return $authUser->can('View:LibraryGmd');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:LibraryGmd');
    }

    public function update(AuthUser $authUser, LibraryGmd $libraryGmd): bool
    {
        return $authUser->can('Update:LibraryGmd');
    }

    public function delete(AuthUser $authUser, LibraryGmd $libraryGmd): bool
    {
        return $authUser->can('Delete:LibraryGmd');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:LibraryGmd');
    }

    public function restore(AuthUser $authUser, LibraryGmd $libraryGmd): bool
    {
        return $authUser->can('Restore:LibraryGmd');
    }

    public function forceDelete(AuthUser $authUser, LibraryGmd $libraryGmd): bool
    {
        return $authUser->can('ForceDelete:LibraryGmd');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:LibraryGmd');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:LibraryGmd');
    }

    public function replicate(AuthUser $authUser, LibraryGmd $libraryGmd): bool
    {
        return $authUser->can('Replicate:LibraryGmd');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:LibraryGmd');
    }
}
