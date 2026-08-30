<?php

declare(strict_types=1);

namespace Modules\Boarding\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Boarding\Models\RoomInspection;

class RoomInspectionPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:RoomInspection');
    }

    public function view(AuthUser $authUser, RoomInspection $roomInspection): bool
    {
        return $authUser->can('View:RoomInspection');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:RoomInspection');
    }

    public function update(AuthUser $authUser, RoomInspection $roomInspection): bool
    {
        return $authUser->can('Update:RoomInspection');
    }

    public function delete(AuthUser $authUser, RoomInspection $roomInspection): bool
    {
        return $authUser->can('Delete:RoomInspection');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:RoomInspection');
    }

    public function restore(AuthUser $authUser, RoomInspection $roomInspection): bool
    {
        return $authUser->can('Restore:RoomInspection');
    }

    public function forceDelete(AuthUser $authUser, RoomInspection $roomInspection): bool
    {
        return $authUser->can('ForceDelete:RoomInspection');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:RoomInspection');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:RoomInspection');
    }

    public function replicate(AuthUser $authUser, RoomInspection $roomInspection): bool
    {
        return $authUser->can('Replicate:RoomInspection');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:RoomInspection');
    }
}
