<?php

declare(strict_types=1);

namespace Modules\Boarding\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Boarding\Models\BoardingAttendance;

class BoardingAttendancePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:BoardingAttendance');
    }

    public function view(AuthUser $authUser, BoardingAttendance $boardingAttendance): bool
    {
        return $authUser->can('View:BoardingAttendance');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:BoardingAttendance');
    }

    public function update(AuthUser $authUser, BoardingAttendance $boardingAttendance): bool
    {
        return $authUser->can('Update:BoardingAttendance');
    }

    public function delete(AuthUser $authUser, BoardingAttendance $boardingAttendance): bool
    {
        return $authUser->can('Delete:BoardingAttendance');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:BoardingAttendance');
    }

    public function restore(AuthUser $authUser, BoardingAttendance $boardingAttendance): bool
    {
        return $authUser->can('Restore:BoardingAttendance');
    }

    public function forceDelete(AuthUser $authUser, BoardingAttendance $boardingAttendance): bool
    {
        return $authUser->can('ForceDelete:BoardingAttendance');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:BoardingAttendance');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:BoardingAttendance');
    }

    public function replicate(AuthUser $authUser, BoardingAttendance $boardingAttendance): bool
    {
        return $authUser->can('Replicate:BoardingAttendance');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:BoardingAttendance');
    }
}
