<?php

declare(strict_types=1);

namespace Modules\Counseling\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Counseling\Models\CaseFollowUp;

class CaseFollowUpPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CaseFollowUp');
    }

    public function view(AuthUser $authUser, CaseFollowUp $caseFollowUp): bool
    {
        return $authUser->can('View:CaseFollowUp');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CaseFollowUp');
    }

    public function update(AuthUser $authUser, CaseFollowUp $caseFollowUp): bool
    {
        return $authUser->can('Update:CaseFollowUp');
    }

    public function delete(AuthUser $authUser, CaseFollowUp $caseFollowUp): bool
    {
        return $authUser->can('Delete:CaseFollowUp');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:CaseFollowUp');
    }

    public function restore(AuthUser $authUser, CaseFollowUp $caseFollowUp): bool
    {
        return $authUser->can('Restore:CaseFollowUp');
    }

    public function forceDelete(AuthUser $authUser, CaseFollowUp $caseFollowUp): bool
    {
        return $authUser->can('ForceDelete:CaseFollowUp');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CaseFollowUp');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CaseFollowUp');
    }

    public function replicate(AuthUser $authUser, CaseFollowUp $caseFollowUp): bool
    {
        return $authUser->can('Replicate:CaseFollowUp');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CaseFollowUp');
    }
}
