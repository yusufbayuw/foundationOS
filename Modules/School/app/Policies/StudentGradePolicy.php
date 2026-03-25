<?php

declare(strict_types=1);

namespace Modules\School\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\School\Models\StudentGrade;
use Illuminate\Auth\Access\HandlesAuthorization;

class StudentGradePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:StudentGrade');
    }

    public function view(AuthUser $authUser, StudentGrade $studentGrade): bool
    {
        return $authUser->can('View:StudentGrade');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:StudentGrade');
    }

    public function update(AuthUser $authUser, StudentGrade $studentGrade): bool
    {
        return $authUser->can('Update:StudentGrade');
    }

    public function delete(AuthUser $authUser, StudentGrade $studentGrade): bool
    {
        return $authUser->can('Delete:StudentGrade');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:StudentGrade');
    }

    public function restore(AuthUser $authUser, StudentGrade $studentGrade): bool
    {
        return $authUser->can('Restore:StudentGrade');
    }

    public function forceDelete(AuthUser $authUser, StudentGrade $studentGrade): bool
    {
        return $authUser->can('ForceDelete:StudentGrade');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:StudentGrade');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:StudentGrade');
    }

    public function replicate(AuthUser $authUser, StudentGrade $studentGrade): bool
    {
        return $authUser->can('Replicate:StudentGrade');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:StudentGrade');
    }

}