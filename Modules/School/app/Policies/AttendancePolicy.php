<?php

declare(strict_types=1);

namespace Modules\School\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\School\Models\Attendance;

class AttendancePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Attendance');
    }

    public function view(AuthUser $authUser, Attendance $attendance): bool
    {
        return $authUser->can('View:Attendance');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Attendance');
    }

    public function update(AuthUser $authUser, Attendance $attendance): bool
    {
        return $authUser->can('Update:Attendance');
    }

    public function delete(AuthUser $authUser, Attendance $attendance): bool
    {
        return $authUser->can('Delete:Attendance');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Attendance');
    }

    public function restore(AuthUser $authUser, Attendance $attendance): bool
    {
        return $authUser->can('Restore:Attendance');
    }

    public function forceDelete(AuthUser $authUser, Attendance $attendance): bool
    {
        return $authUser->can('ForceDelete:Attendance');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Attendance');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Attendance');
    }

    public function replicate(AuthUser $authUser, Attendance $attendance): bool
    {
        return $authUser->can('Replicate:Attendance');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Attendance');
    }
}
