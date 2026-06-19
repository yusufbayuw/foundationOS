<?php

declare(strict_types=1);

namespace Modules\Library\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Library\Models\LibraryFrequency;

class LibraryFrequencyPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:LibraryFrequency');
    }

    public function view(AuthUser $authUser, LibraryFrequency $libraryFrequency): bool
    {
        return $authUser->can('View:LibraryFrequency');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:LibraryFrequency');
    }

    public function update(AuthUser $authUser, LibraryFrequency $libraryFrequency): bool
    {
        return $authUser->can('Update:LibraryFrequency');
    }

    public function delete(AuthUser $authUser, LibraryFrequency $libraryFrequency): bool
    {
        return $authUser->can('Delete:LibraryFrequency');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:LibraryFrequency');
    }

    public function restore(AuthUser $authUser, LibraryFrequency $libraryFrequency): bool
    {
        return $authUser->can('Restore:LibraryFrequency');
    }

    public function forceDelete(AuthUser $authUser, LibraryFrequency $libraryFrequency): bool
    {
        return $authUser->can('ForceDelete:LibraryFrequency');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:LibraryFrequency');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:LibraryFrequency');
    }

    public function replicate(AuthUser $authUser, LibraryFrequency $libraryFrequency): bool
    {
        return $authUser->can('Replicate:LibraryFrequency');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:LibraryFrequency');
    }
}
