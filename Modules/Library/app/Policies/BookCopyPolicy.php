<?php

declare(strict_types=1);

namespace Modules\Library\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Library\Models\BookCopy;

class BookCopyPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:BookCopy');
    }

    public function view(AuthUser $authUser, BookCopy $bookCopy): bool
    {
        return $authUser->can('View:BookCopy');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:BookCopy');
    }

    public function update(AuthUser $authUser, BookCopy $bookCopy): bool
    {
        return $authUser->can('Update:BookCopy');
    }

    public function delete(AuthUser $authUser, BookCopy $bookCopy): bool
    {
        return $authUser->can('Delete:BookCopy');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:BookCopy');
    }

    public function restore(AuthUser $authUser, BookCopy $bookCopy): bool
    {
        return $authUser->can('Restore:BookCopy');
    }

    public function forceDelete(AuthUser $authUser, BookCopy $bookCopy): bool
    {
        return $authUser->can('ForceDelete:BookCopy');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:BookCopy');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:BookCopy');
    }

    public function replicate(AuthUser $authUser, BookCopy $bookCopy): bool
    {
        return $authUser->can('Replicate:BookCopy');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:BookCopy');
    }
}
