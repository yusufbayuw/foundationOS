<?php

declare(strict_types=1);

namespace Modules\Library\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Library\Models\LibraryMemberType;

class LibraryMemberTypePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:LibraryMemberType');
    }

    public function view(AuthUser $authUser, LibraryMemberType $libraryMemberType): bool
    {
        return $authUser->can('View:LibraryMemberType');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:LibraryMemberType');
    }

    public function update(AuthUser $authUser, LibraryMemberType $libraryMemberType): bool
    {
        return $authUser->can('Update:LibraryMemberType');
    }

    public function delete(AuthUser $authUser, LibraryMemberType $libraryMemberType): bool
    {
        return $authUser->can('Delete:LibraryMemberType');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:LibraryMemberType');
    }

    public function restore(AuthUser $authUser, LibraryMemberType $libraryMemberType): bool
    {
        return $authUser->can('Restore:LibraryMemberType');
    }

    public function forceDelete(AuthUser $authUser, LibraryMemberType $libraryMemberType): bool
    {
        return $authUser->can('ForceDelete:LibraryMemberType');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:LibraryMemberType');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:LibraryMemberType');
    }

    public function replicate(AuthUser $authUser, LibraryMemberType $libraryMemberType): bool
    {
        return $authUser->can('Replicate:LibraryMemberType');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:LibraryMemberType');
    }
}
