<?php

declare(strict_types=1);

namespace Modules\Training\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Training\Models\TrainingEnrollment;

class TrainingEnrollmentPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:TrainingEnrollment');
    }

    public function view(AuthUser $authUser, TrainingEnrollment $trainingEnrollment): bool
    {
        return $authUser->can('View:TrainingEnrollment');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:TrainingEnrollment');
    }

    public function update(AuthUser $authUser, TrainingEnrollment $trainingEnrollment): bool
    {
        return $authUser->can('Update:TrainingEnrollment');
    }

    public function delete(AuthUser $authUser, TrainingEnrollment $trainingEnrollment): bool
    {
        return $authUser->can('Delete:TrainingEnrollment');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:TrainingEnrollment');
    }

    public function restore(AuthUser $authUser, TrainingEnrollment $trainingEnrollment): bool
    {
        return $authUser->can('Restore:TrainingEnrollment');
    }

    public function forceDelete(AuthUser $authUser, TrainingEnrollment $trainingEnrollment): bool
    {
        return $authUser->can('ForceDelete:TrainingEnrollment');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:TrainingEnrollment');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:TrainingEnrollment');
    }

    public function replicate(AuthUser $authUser, TrainingEnrollment $trainingEnrollment): bool
    {
        return $authUser->can('Replicate:TrainingEnrollment');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:TrainingEnrollment');
    }
}
