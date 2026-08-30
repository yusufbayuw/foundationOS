<?php

declare(strict_types=1);

namespace Modules\School\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\School\Models\StudentAbsence;

class StudentAbsencePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:StudentAbsence');
    }

    public function view(AuthUser $authUser, StudentAbsence $studentAbsence): bool
    {
        return $authUser->can('View:StudentAbsence');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:StudentAbsence');
    }

    public function update(AuthUser $authUser, StudentAbsence $studentAbsence): bool
    {
        return $authUser->can('Update:StudentAbsence');
    }

    public function delete(AuthUser $authUser, StudentAbsence $studentAbsence): bool
    {
        return $authUser->can('Delete:StudentAbsence');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:StudentAbsence');
    }

    public function restore(AuthUser $authUser, StudentAbsence $studentAbsence): bool
    {
        return $authUser->can('Restore:StudentAbsence');
    }

    public function forceDelete(AuthUser $authUser, StudentAbsence $studentAbsence): bool
    {
        return $authUser->can('ForceDelete:StudentAbsence');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:StudentAbsence');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:StudentAbsence');
    }

    public function replicate(AuthUser $authUser, StudentAbsence $studentAbsence): bool
    {
        return $authUser->can('Replicate:StudentAbsence');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:StudentAbsence');
    }
}
