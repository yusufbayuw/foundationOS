<?php

declare(strict_types=1);

namespace Modules\Printing\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Printing\Models\PublicationAuthor;

class PublicationAuthorPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PublicationAuthor');
    }

    public function view(AuthUser $authUser, PublicationAuthor $publicationAuthor): bool
    {
        return $authUser->can('View:PublicationAuthor');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PublicationAuthor');
    }

    public function update(AuthUser $authUser, PublicationAuthor $publicationAuthor): bool
    {
        return $authUser->can('Update:PublicationAuthor');
    }

    public function delete(AuthUser $authUser, PublicationAuthor $publicationAuthor): bool
    {
        return $authUser->can('Delete:PublicationAuthor');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PublicationAuthor');
    }

    public function restore(AuthUser $authUser, PublicationAuthor $publicationAuthor): bool
    {
        return $authUser->can('Restore:PublicationAuthor');
    }

    public function forceDelete(AuthUser $authUser, PublicationAuthor $publicationAuthor): bool
    {
        return $authUser->can('ForceDelete:PublicationAuthor');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PublicationAuthor');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PublicationAuthor');
    }

    public function replicate(AuthUser $authUser, PublicationAuthor $publicationAuthor): bool
    {
        return $authUser->can('Replicate:PublicationAuthor');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PublicationAuthor');
    }
}
