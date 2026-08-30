<?php

declare(strict_types=1);

namespace Modules\Dms\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Dms\Models\DocumentVersion;

class DocumentVersionPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:DocumentVersion');
    }

    public function view(AuthUser $authUser, DocumentVersion $documentVersion): bool
    {
        return $authUser->can('View:DocumentVersion');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:DocumentVersion');
    }

    public function update(AuthUser $authUser, DocumentVersion $documentVersion): bool
    {
        return $authUser->can('Update:DocumentVersion');
    }

    public function delete(AuthUser $authUser, DocumentVersion $documentVersion): bool
    {
        return $authUser->can('Delete:DocumentVersion');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:DocumentVersion');
    }

    public function restore(AuthUser $authUser, DocumentVersion $documentVersion): bool
    {
        return $authUser->can('Restore:DocumentVersion');
    }

    public function forceDelete(AuthUser $authUser, DocumentVersion $documentVersion): bool
    {
        return $authUser->can('ForceDelete:DocumentVersion');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:DocumentVersion');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:DocumentVersion');
    }

    public function replicate(AuthUser $authUser, DocumentVersion $documentVersion): bool
    {
        return $authUser->can('Replicate:DocumentVersion');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:DocumentVersion');
    }
}
