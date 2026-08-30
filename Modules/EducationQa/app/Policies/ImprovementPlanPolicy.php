<?php

declare(strict_types=1);

namespace Modules\EducationQa\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\EducationQa\Models\ImprovementPlan;

class ImprovementPlanPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ImprovementPlan');
    }

    public function view(AuthUser $authUser, ImprovementPlan $improvementPlan): bool
    {
        return $authUser->can('View:ImprovementPlan');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ImprovementPlan');
    }

    public function update(AuthUser $authUser, ImprovementPlan $improvementPlan): bool
    {
        return $authUser->can('Update:ImprovementPlan');
    }

    public function delete(AuthUser $authUser, ImprovementPlan $improvementPlan): bool
    {
        return $authUser->can('Delete:ImprovementPlan');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ImprovementPlan');
    }

    public function restore(AuthUser $authUser, ImprovementPlan $improvementPlan): bool
    {
        return $authUser->can('Restore:ImprovementPlan');
    }

    public function forceDelete(AuthUser $authUser, ImprovementPlan $improvementPlan): bool
    {
        return $authUser->can('ForceDelete:ImprovementPlan');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ImprovementPlan');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ImprovementPlan');
    }

    public function replicate(AuthUser $authUser, ImprovementPlan $improvementPlan): bool
    {
        return $authUser->can('Replicate:ImprovementPlan');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ImprovementPlan');
    }
}
