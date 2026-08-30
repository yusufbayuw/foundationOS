<?php

declare(strict_types=1);

namespace Modules\Facility\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Facility\Models\RoomEquipment;

class RoomEquipmentPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:RoomEquipment');
    }

    public function view(AuthUser $authUser, RoomEquipment $roomEquipment): bool
    {
        return $authUser->can('View:RoomEquipment');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:RoomEquipment');
    }

    public function update(AuthUser $authUser, RoomEquipment $roomEquipment): bool
    {
        return $authUser->can('Update:RoomEquipment');
    }

    public function delete(AuthUser $authUser, RoomEquipment $roomEquipment): bool
    {
        return $authUser->can('Delete:RoomEquipment');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:RoomEquipment');
    }

    public function restore(AuthUser $authUser, RoomEquipment $roomEquipment): bool
    {
        return $authUser->can('Restore:RoomEquipment');
    }

    public function forceDelete(AuthUser $authUser, RoomEquipment $roomEquipment): bool
    {
        return $authUser->can('ForceDelete:RoomEquipment');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:RoomEquipment');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:RoomEquipment');
    }

    public function replicate(AuthUser $authUser, RoomEquipment $roomEquipment): bool
    {
        return $authUser->can('Replicate:RoomEquipment');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:RoomEquipment');
    }
}
