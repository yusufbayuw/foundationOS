<?php

declare(strict_types=1);

namespace Modules\EducationQa\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\EducationQa\Models\QualitySurvey;

class QualitySurveyPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:QualitySurvey');
    }

    public function view(AuthUser $authUser, QualitySurvey $qualitySurvey): bool
    {
        return $authUser->can('View:QualitySurvey');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:QualitySurvey');
    }

    public function update(AuthUser $authUser, QualitySurvey $qualitySurvey): bool
    {
        return $authUser->can('Update:QualitySurvey');
    }

    public function delete(AuthUser $authUser, QualitySurvey $qualitySurvey): bool
    {
        return $authUser->can('Delete:QualitySurvey');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:QualitySurvey');
    }

    public function restore(AuthUser $authUser, QualitySurvey $qualitySurvey): bool
    {
        return $authUser->can('Restore:QualitySurvey');
    }

    public function forceDelete(AuthUser $authUser, QualitySurvey $qualitySurvey): bool
    {
        return $authUser->can('ForceDelete:QualitySurvey');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:QualitySurvey');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:QualitySurvey');
    }

    public function replicate(AuthUser $authUser, QualitySurvey $qualitySurvey): bool
    {
        return $authUser->can('Replicate:QualitySurvey');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:QualitySurvey');
    }
}
