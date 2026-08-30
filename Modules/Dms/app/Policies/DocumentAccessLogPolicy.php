<?php

declare(strict_types=1);

namespace Modules\Dms\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Dms\Models\DocumentAccessLog;

class DocumentAccessLogPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:DocumentAccessLog');
    }

    public function view(AuthUser $authUser, DocumentAccessLog $documentAccessLog): bool
    {
        return $authUser->can('View:DocumentAccessLog');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:DocumentAccessLog');
    }

    public function update(AuthUser $authUser, DocumentAccessLog $documentAccessLog): bool
    {
        return $authUser->can('Update:DocumentAccessLog');
    }

    public function delete(AuthUser $authUser, DocumentAccessLog $documentAccessLog): bool
    {
        return $authUser->can('Delete:DocumentAccessLog');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:DocumentAccessLog');
    }

    public function restore(AuthUser $authUser, DocumentAccessLog $documentAccessLog): bool
    {
        return $authUser->can('Restore:DocumentAccessLog');
    }

    public function forceDelete(AuthUser $authUser, DocumentAccessLog $documentAccessLog): bool
    {
        return $authUser->can('ForceDelete:DocumentAccessLog');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:DocumentAccessLog');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:DocumentAccessLog');
    }

    public function replicate(AuthUser $authUser, DocumentAccessLog $documentAccessLog): bool
    {
        return $authUser->can('Replicate:DocumentAccessLog');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:DocumentAccessLog');
    }
}
