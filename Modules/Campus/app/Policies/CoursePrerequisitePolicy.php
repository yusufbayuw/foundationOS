<?php

declare(strict_types=1);

namespace Modules\Campus\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Campus\Models\CoursePrerequisite;

class CoursePrerequisitePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CoursePrerequisite');
    }

    public function view(AuthUser $authUser, CoursePrerequisite $coursePrerequisite): bool
    {
        return $authUser->can('View:CoursePrerequisite');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CoursePrerequisite');
    }

    public function update(AuthUser $authUser, CoursePrerequisite $coursePrerequisite): bool
    {
        return $authUser->can('Update:CoursePrerequisite');
    }

    public function delete(AuthUser $authUser, CoursePrerequisite $coursePrerequisite): bool
    {
        return $authUser->can('Delete:CoursePrerequisite');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:CoursePrerequisite');
    }

    public function restore(AuthUser $authUser, CoursePrerequisite $coursePrerequisite): bool
    {
        return $authUser->can('Restore:CoursePrerequisite');
    }

    public function forceDelete(AuthUser $authUser, CoursePrerequisite $coursePrerequisite): bool
    {
        return $authUser->can('ForceDelete:CoursePrerequisite');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CoursePrerequisite');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CoursePrerequisite');
    }

    public function replicate(AuthUser $authUser, CoursePrerequisite $coursePrerequisite): bool
    {
        return $authUser->can('Replicate:CoursePrerequisite');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CoursePrerequisite');
    }
}
