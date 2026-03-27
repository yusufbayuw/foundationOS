<?php

declare(strict_types=1);

namespace Modules\Library\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Library\Models\LibrarySubject;
use Illuminate\Auth\Access\HandlesAuthorization;

class LibrarySubjectPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:LibrarySubject');
    }

    public function view(AuthUser $authUser, LibrarySubject $librarySubject): bool
    {
        return $authUser->can('View:LibrarySubject');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:LibrarySubject');
    }

    public function update(AuthUser $authUser, LibrarySubject $librarySubject): bool
    {
        return $authUser->can('Update:LibrarySubject');
    }

    public function delete(AuthUser $authUser, LibrarySubject $librarySubject): bool
    {
        return $authUser->can('Delete:LibrarySubject');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:LibrarySubject');
    }

    public function restore(AuthUser $authUser, LibrarySubject $librarySubject): bool
    {
        return $authUser->can('Restore:LibrarySubject');
    }

    public function forceDelete(AuthUser $authUser, LibrarySubject $librarySubject): bool
    {
        return $authUser->can('ForceDelete:LibrarySubject');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:LibrarySubject');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:LibrarySubject');
    }

    public function replicate(AuthUser $authUser, LibrarySubject $librarySubject): bool
    {
        return $authUser->can('Replicate:LibrarySubject');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:LibrarySubject');
    }

}