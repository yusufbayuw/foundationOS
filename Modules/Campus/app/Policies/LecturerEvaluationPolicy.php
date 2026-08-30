<?php

declare(strict_types=1);

namespace Modules\Campus\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Campus\Models\LecturerEvaluation;

class LecturerEvaluationPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:LecturerEvaluation');
    }

    public function view(AuthUser $authUser, LecturerEvaluation $lecturerEvaluation): bool
    {
        return $authUser->can('View:LecturerEvaluation');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:LecturerEvaluation');
    }

    public function update(AuthUser $authUser, LecturerEvaluation $lecturerEvaluation): bool
    {
        return $authUser->can('Update:LecturerEvaluation');
    }

    public function delete(AuthUser $authUser, LecturerEvaluation $lecturerEvaluation): bool
    {
        return $authUser->can('Delete:LecturerEvaluation');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:LecturerEvaluation');
    }

    public function restore(AuthUser $authUser, LecturerEvaluation $lecturerEvaluation): bool
    {
        return $authUser->can('Restore:LecturerEvaluation');
    }

    public function forceDelete(AuthUser $authUser, LecturerEvaluation $lecturerEvaluation): bool
    {
        return $authUser->can('ForceDelete:LecturerEvaluation');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:LecturerEvaluation');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:LecturerEvaluation');
    }

    public function replicate(AuthUser $authUser, LecturerEvaluation $lecturerEvaluation): bool
    {
        return $authUser->can('Replicate:LecturerEvaluation');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:LecturerEvaluation');
    }
}
