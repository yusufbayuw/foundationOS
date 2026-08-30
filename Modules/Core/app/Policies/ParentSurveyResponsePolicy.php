<?php

declare(strict_types=1);

namespace Modules\Core\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Core\Models\ParentSurveyResponse;

class ParentSurveyResponsePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ParentSurveyResponse');
    }

    public function view(AuthUser $authUser, ParentSurveyResponse $parentSurveyResponse): bool
    {
        return $authUser->can('View:ParentSurveyResponse');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ParentSurveyResponse');
    }

    public function update(AuthUser $authUser, ParentSurveyResponse $parentSurveyResponse): bool
    {
        return $authUser->can('Update:ParentSurveyResponse');
    }

    public function delete(AuthUser $authUser, ParentSurveyResponse $parentSurveyResponse): bool
    {
        return $authUser->can('Delete:ParentSurveyResponse');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ParentSurveyResponse');
    }

    public function restore(AuthUser $authUser, ParentSurveyResponse $parentSurveyResponse): bool
    {
        return $authUser->can('Restore:ParentSurveyResponse');
    }

    public function forceDelete(AuthUser $authUser, ParentSurveyResponse $parentSurveyResponse): bool
    {
        return $authUser->can('ForceDelete:ParentSurveyResponse');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ParentSurveyResponse');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ParentSurveyResponse');
    }

    public function replicate(AuthUser $authUser, ParentSurveyResponse $parentSurveyResponse): bool
    {
        return $authUser->can('Replicate:ParentSurveyResponse');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ParentSurveyResponse');
    }
}
