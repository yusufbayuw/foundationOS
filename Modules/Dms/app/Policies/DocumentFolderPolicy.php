<?php

declare(strict_types=1);

namespace Modules\Dms\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Dms\Models\DocumentFolder;

class DocumentFolderPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:DocumentFolder');
    }

    public function view(AuthUser $authUser, DocumentFolder $documentFolder): bool
    {
        return $authUser->can('View:DocumentFolder');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:DocumentFolder');
    }

    public function update(AuthUser $authUser, DocumentFolder $documentFolder): bool
    {
        return $authUser->can('Update:DocumentFolder');
    }

    public function delete(AuthUser $authUser, DocumentFolder $documentFolder): bool
    {
        return $authUser->can('Delete:DocumentFolder');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:DocumentFolder');
    }

    public function restore(AuthUser $authUser, DocumentFolder $documentFolder): bool
    {
        return $authUser->can('Restore:DocumentFolder');
    }

    public function forceDelete(AuthUser $authUser, DocumentFolder $documentFolder): bool
    {
        return $authUser->can('ForceDelete:DocumentFolder');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:DocumentFolder');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:DocumentFolder');
    }

    public function replicate(AuthUser $authUser, DocumentFolder $documentFolder): bool
    {
        return $authUser->can('Replicate:DocumentFolder');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:DocumentFolder');
    }
}
