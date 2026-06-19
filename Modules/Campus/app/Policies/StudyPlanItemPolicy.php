<?php

declare(strict_types=1);

namespace Modules\Campus\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Campus\Models\StudyPlanItem;

class StudyPlanItemPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:StudyPlanItem');
    }

    public function view(AuthUser $authUser, StudyPlanItem $studyPlanItem): bool
    {
        return $authUser->can('View:StudyPlanItem');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:StudyPlanItem');
    }

    public function update(AuthUser $authUser, StudyPlanItem $studyPlanItem): bool
    {
        return $authUser->can('Update:StudyPlanItem');
    }

    public function delete(AuthUser $authUser, StudyPlanItem $studyPlanItem): bool
    {
        return $authUser->can('Delete:StudyPlanItem');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:StudyPlanItem');
    }

    public function restore(AuthUser $authUser, StudyPlanItem $studyPlanItem): bool
    {
        return $authUser->can('Restore:StudyPlanItem');
    }

    public function forceDelete(AuthUser $authUser, StudyPlanItem $studyPlanItem): bool
    {
        return $authUser->can('ForceDelete:StudyPlanItem');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:StudyPlanItem');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:StudyPlanItem');
    }

    public function replicate(AuthUser $authUser, StudyPlanItem $studyPlanItem): bool
    {
        return $authUser->can('Replicate:StudyPlanItem');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:StudyPlanItem');
    }
}
