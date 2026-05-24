<?php

declare(strict_types=1);

namespace Modules\Counseling\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Counseling\Models\CounselingCase;

class CounselingCasePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CounselingCase');
    }

    public function view(AuthUser $authUser, CounselingCase $counselingCase): bool
    {
        return $authUser->can('View:CounselingCase');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CounselingCase');
    }

    public function update(AuthUser $authUser, CounselingCase $counselingCase): bool
    {
        return $authUser->can('Update:CounselingCase');
    }

    public function delete(AuthUser $authUser, CounselingCase $counselingCase): bool
    {
        return $authUser->can('Delete:CounselingCase');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:CounselingCase');
    }

    public function restore(AuthUser $authUser, CounselingCase $counselingCase): bool
    {
        return $authUser->can('Restore:CounselingCase');
    }

    public function forceDelete(AuthUser $authUser, CounselingCase $counselingCase): bool
    {
        return $authUser->can('ForceDelete:CounselingCase');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CounselingCase');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CounselingCase');
    }

    public function replicate(AuthUser $authUser, CounselingCase $counselingCase): bool
    {
        return $authUser->can('Replicate:CounselingCase');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CounselingCase');
    }
}
