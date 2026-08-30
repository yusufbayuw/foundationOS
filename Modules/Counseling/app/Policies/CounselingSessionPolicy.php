<?php

declare(strict_types=1);

namespace Modules\Counseling\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Counseling\Models\CounselingSession;

class CounselingSessionPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CounselingSession');
    }

    public function view(AuthUser $authUser, CounselingSession $counselingSession): bool
    {
        return $authUser->can('View:CounselingSession');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CounselingSession');
    }

    public function update(AuthUser $authUser, CounselingSession $counselingSession): bool
    {
        return $authUser->can('Update:CounselingSession');
    }

    public function delete(AuthUser $authUser, CounselingSession $counselingSession): bool
    {
        return $authUser->can('Delete:CounselingSession');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:CounselingSession');
    }

    public function restore(AuthUser $authUser, CounselingSession $counselingSession): bool
    {
        return $authUser->can('Restore:CounselingSession');
    }

    public function forceDelete(AuthUser $authUser, CounselingSession $counselingSession): bool
    {
        return $authUser->can('ForceDelete:CounselingSession');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CounselingSession');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CounselingSession');
    }

    public function replicate(AuthUser $authUser, CounselingSession $counselingSession): bool
    {
        return $authUser->can('Replicate:CounselingSession');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CounselingSession');
    }
}
