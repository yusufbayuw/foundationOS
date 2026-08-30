<?php

declare(strict_types=1);

namespace Modules\Campus\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Campus\Models\CourseOfferingLecturer;

class CourseOfferingLecturerPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CourseOfferingLecturer');
    }

    public function view(AuthUser $authUser, CourseOfferingLecturer $courseOfferingLecturer): bool
    {
        return $authUser->can('View:CourseOfferingLecturer');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CourseOfferingLecturer');
    }

    public function update(AuthUser $authUser, CourseOfferingLecturer $courseOfferingLecturer): bool
    {
        return $authUser->can('Update:CourseOfferingLecturer');
    }

    public function delete(AuthUser $authUser, CourseOfferingLecturer $courseOfferingLecturer): bool
    {
        return $authUser->can('Delete:CourseOfferingLecturer');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:CourseOfferingLecturer');
    }

    public function restore(AuthUser $authUser, CourseOfferingLecturer $courseOfferingLecturer): bool
    {
        return $authUser->can('Restore:CourseOfferingLecturer');
    }

    public function forceDelete(AuthUser $authUser, CourseOfferingLecturer $courseOfferingLecturer): bool
    {
        return $authUser->can('ForceDelete:CourseOfferingLecturer');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CourseOfferingLecturer');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CourseOfferingLecturer');
    }

    public function replicate(AuthUser $authUser, CourseOfferingLecturer $courseOfferingLecturer): bool
    {
        return $authUser->can('Replicate:CourseOfferingLecturer');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CourseOfferingLecturer');
    }
}
