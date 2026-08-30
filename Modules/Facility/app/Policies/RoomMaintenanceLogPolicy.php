<?php

declare(strict_types=1);

namespace Modules\Facility\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Facility\Models\RoomMaintenanceLog;

class RoomMaintenanceLogPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:RoomMaintenanceLog');
    }

    public function view(AuthUser $authUser, RoomMaintenanceLog $roomMaintenanceLog): bool
    {
        return $authUser->can('View:RoomMaintenanceLog');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:RoomMaintenanceLog');
    }

    public function update(AuthUser $authUser, RoomMaintenanceLog $roomMaintenanceLog): bool
    {
        return $authUser->can('Update:RoomMaintenanceLog');
    }

    public function delete(AuthUser $authUser, RoomMaintenanceLog $roomMaintenanceLog): bool
    {
        return $authUser->can('Delete:RoomMaintenanceLog');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:RoomMaintenanceLog');
    }

    public function restore(AuthUser $authUser, RoomMaintenanceLog $roomMaintenanceLog): bool
    {
        return $authUser->can('Restore:RoomMaintenanceLog');
    }

    public function forceDelete(AuthUser $authUser, RoomMaintenanceLog $roomMaintenanceLog): bool
    {
        return $authUser->can('ForceDelete:RoomMaintenanceLog');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:RoomMaintenanceLog');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:RoomMaintenanceLog');
    }

    public function replicate(AuthUser $authUser, RoomMaintenanceLog $roomMaintenanceLog): bool
    {
        return $authUser->can('Replicate:RoomMaintenanceLog');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:RoomMaintenanceLog');
    }
}
