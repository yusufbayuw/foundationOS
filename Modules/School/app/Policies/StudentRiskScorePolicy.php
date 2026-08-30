<?php

declare(strict_types=1);

namespace Modules\School\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\School\Models\StudentRiskScore;

class StudentRiskScorePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:StudentRiskScore');
    }

    public function view(AuthUser $authUser, StudentRiskScore $studentRiskScore): bool
    {
        return $authUser->can('View:StudentRiskScore');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:StudentRiskScore');
    }

    public function update(AuthUser $authUser, StudentRiskScore $studentRiskScore): bool
    {
        return $authUser->can('Update:StudentRiskScore');
    }

    public function delete(AuthUser $authUser, StudentRiskScore $studentRiskScore): bool
    {
        return $authUser->can('Delete:StudentRiskScore');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:StudentRiskScore');
    }

    public function restore(AuthUser $authUser, StudentRiskScore $studentRiskScore): bool
    {
        return $authUser->can('Restore:StudentRiskScore');
    }

    public function forceDelete(AuthUser $authUser, StudentRiskScore $studentRiskScore): bool
    {
        return $authUser->can('ForceDelete:StudentRiskScore');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:StudentRiskScore');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:StudentRiskScore');
    }

    public function replicate(AuthUser $authUser, StudentRiskScore $studentRiskScore): bool
    {
        return $authUser->can('Replicate:StudentRiskScore');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:StudentRiskScore');
    }
}
