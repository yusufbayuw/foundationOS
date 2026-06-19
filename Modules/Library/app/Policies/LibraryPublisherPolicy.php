<?php

declare(strict_types=1);

namespace Modules\Library\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Library\Models\LibraryPublisher;

class LibraryPublisherPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:LibraryPublisher');
    }

    public function view(AuthUser $authUser, LibraryPublisher $libraryPublisher): bool
    {
        return $authUser->can('View:LibraryPublisher');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:LibraryPublisher');
    }

    public function update(AuthUser $authUser, LibraryPublisher $libraryPublisher): bool
    {
        return $authUser->can('Update:LibraryPublisher');
    }

    public function delete(AuthUser $authUser, LibraryPublisher $libraryPublisher): bool
    {
        return $authUser->can('Delete:LibraryPublisher');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:LibraryPublisher');
    }

    public function restore(AuthUser $authUser, LibraryPublisher $libraryPublisher): bool
    {
        return $authUser->can('Restore:LibraryPublisher');
    }

    public function forceDelete(AuthUser $authUser, LibraryPublisher $libraryPublisher): bool
    {
        return $authUser->can('ForceDelete:LibraryPublisher');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:LibraryPublisher');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:LibraryPublisher');
    }

    public function replicate(AuthUser $authUser, LibraryPublisher $libraryPublisher): bool
    {
        return $authUser->can('Replicate:LibraryPublisher');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:LibraryPublisher');
    }
}
