<?php

declare(strict_types=1);

namespace Modules\Boarding\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Boarding\Models\RoomAssignment;

class RoomAssignmentPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:RoomAssignment');
    }

    public function view(AuthUser $authUser, RoomAssignment $roomAssignment): bool
    {
        return $authUser->can('View:RoomAssignment');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:RoomAssignment');
    }

    public function update(AuthUser $authUser, RoomAssignment $roomAssignment): bool
    {
        return $authUser->can('Update:RoomAssignment');
    }

    public function delete(AuthUser $authUser, RoomAssignment $roomAssignment): bool
    {
        return $authUser->can('Delete:RoomAssignment');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:RoomAssignment');
    }

    public function restore(AuthUser $authUser, RoomAssignment $roomAssignment): bool
    {
        return $authUser->can('Restore:RoomAssignment');
    }

    public function forceDelete(AuthUser $authUser, RoomAssignment $roomAssignment): bool
    {
        return $authUser->can('ForceDelete:RoomAssignment');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:RoomAssignment');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:RoomAssignment');
    }

    public function replicate(AuthUser $authUser, RoomAssignment $roomAssignment): bool
    {
        return $authUser->can('Replicate:RoomAssignment');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:RoomAssignment');
    }
}
