<?php

declare(strict_types=1);

namespace Modules\EducationQa\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\EducationQa\Models\SurveyResponse;

class SurveyResponsePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SurveyResponse');
    }

    public function view(AuthUser $authUser, SurveyResponse $surveyResponse): bool
    {
        return $authUser->can('View:SurveyResponse');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SurveyResponse');
    }

    public function update(AuthUser $authUser, SurveyResponse $surveyResponse): bool
    {
        return $authUser->can('Update:SurveyResponse');
    }

    public function delete(AuthUser $authUser, SurveyResponse $surveyResponse): bool
    {
        return $authUser->can('Delete:SurveyResponse');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:SurveyResponse');
    }

    public function restore(AuthUser $authUser, SurveyResponse $surveyResponse): bool
    {
        return $authUser->can('Restore:SurveyResponse');
    }

    public function forceDelete(AuthUser $authUser, SurveyResponse $surveyResponse): bool
    {
        return $authUser->can('ForceDelete:SurveyResponse');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SurveyResponse');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SurveyResponse');
    }

    public function replicate(AuthUser $authUser, SurveyResponse $surveyResponse): bool
    {
        return $authUser->can('Replicate:SurveyResponse');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SurveyResponse');
    }
}
