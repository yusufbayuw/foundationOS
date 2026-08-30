<?php

declare(strict_types=1);

namespace Modules\Core\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Core\Models\ParentSurvey;

class ParentSurveyPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ParentSurvey');
    }

    public function view(AuthUser $authUser, ParentSurvey $parentSurvey): bool
    {
        return $authUser->can('View:ParentSurvey');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ParentSurvey');
    }

    public function update(AuthUser $authUser, ParentSurvey $parentSurvey): bool
    {
        return $authUser->can('Update:ParentSurvey');
    }

    public function delete(AuthUser $authUser, ParentSurvey $parentSurvey): bool
    {
        return $authUser->can('Delete:ParentSurvey');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ParentSurvey');
    }

    public function restore(AuthUser $authUser, ParentSurvey $parentSurvey): bool
    {
        return $authUser->can('Restore:ParentSurvey');
    }

    public function forceDelete(AuthUser $authUser, ParentSurvey $parentSurvey): bool
    {
        return $authUser->can('ForceDelete:ParentSurvey');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ParentSurvey');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ParentSurvey');
    }

    public function replicate(AuthUser $authUser, ParentSurvey $parentSurvey): bool
    {
        return $authUser->can('Replicate:ParentSurvey');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ParentSurvey');
    }
}
