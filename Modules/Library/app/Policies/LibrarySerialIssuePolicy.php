<?php

declare(strict_types=1);

namespace Modules\Library\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Library\Models\LibrarySerialIssue;
use Illuminate\Auth\Access\HandlesAuthorization;

class LibrarySerialIssuePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:LibrarySerialIssue');
    }

    public function view(AuthUser $authUser, LibrarySerialIssue $librarySerialIssue): bool
    {
        return $authUser->can('View:LibrarySerialIssue');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:LibrarySerialIssue');
    }

    public function update(AuthUser $authUser, LibrarySerialIssue $librarySerialIssue): bool
    {
        return $authUser->can('Update:LibrarySerialIssue');
    }

    public function delete(AuthUser $authUser, LibrarySerialIssue $librarySerialIssue): bool
    {
        return $authUser->can('Delete:LibrarySerialIssue');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:LibrarySerialIssue');
    }

    public function restore(AuthUser $authUser, LibrarySerialIssue $librarySerialIssue): bool
    {
        return $authUser->can('Restore:LibrarySerialIssue');
    }

    public function forceDelete(AuthUser $authUser, LibrarySerialIssue $librarySerialIssue): bool
    {
        return $authUser->can('ForceDelete:LibrarySerialIssue');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:LibrarySerialIssue');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:LibrarySerialIssue');
    }

    public function replicate(AuthUser $authUser, LibrarySerialIssue $librarySerialIssue): bool
    {
        return $authUser->can('Replicate:LibrarySerialIssue');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:LibrarySerialIssue');
    }

}