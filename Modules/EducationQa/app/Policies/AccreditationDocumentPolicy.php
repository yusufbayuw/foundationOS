<?php

declare(strict_types=1);

namespace Modules\EducationQa\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\EducationQa\Models\AccreditationDocument;

class AccreditationDocumentPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AccreditationDocument');
    }

    public function view(AuthUser $authUser, AccreditationDocument $accreditationDocument): bool
    {
        return $authUser->can('View:AccreditationDocument');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AccreditationDocument');
    }

    public function update(AuthUser $authUser, AccreditationDocument $accreditationDocument): bool
    {
        return $authUser->can('Update:AccreditationDocument');
    }

    public function delete(AuthUser $authUser, AccreditationDocument $accreditationDocument): bool
    {
        return $authUser->can('Delete:AccreditationDocument');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:AccreditationDocument');
    }

    public function restore(AuthUser $authUser, AccreditationDocument $accreditationDocument): bool
    {
        return $authUser->can('Restore:AccreditationDocument');
    }

    public function forceDelete(AuthUser $authUser, AccreditationDocument $accreditationDocument): bool
    {
        return $authUser->can('ForceDelete:AccreditationDocument');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:AccreditationDocument');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:AccreditationDocument');
    }

    public function replicate(AuthUser $authUser, AccreditationDocument $accreditationDocument): bool
    {
        return $authUser->can('Replicate:AccreditationDocument');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:AccreditationDocument');
    }
}
