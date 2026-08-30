<?php

declare(strict_types=1);

namespace Modules\IsoCompliance\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\IsoCompliance\Models\IsoIncident;

class IsoIncidentPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:IsoIncident');
    }

    public function view(AuthUser $authUser, IsoIncident $isoIncident): bool
    {
        return $authUser->can('View:IsoIncident');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:IsoIncident');
    }

    public function update(AuthUser $authUser, IsoIncident $isoIncident): bool
    {
        return $authUser->can('Update:IsoIncident');
    }

    public function delete(AuthUser $authUser, IsoIncident $isoIncident): bool
    {
        return $authUser->can('Delete:IsoIncident');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:IsoIncident');
    }

    public function restore(AuthUser $authUser, IsoIncident $isoIncident): bool
    {
        return $authUser->can('Restore:IsoIncident');
    }

    public function forceDelete(AuthUser $authUser, IsoIncident $isoIncident): bool
    {
        return $authUser->can('ForceDelete:IsoIncident');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:IsoIncident');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:IsoIncident');
    }

    public function replicate(AuthUser $authUser, IsoIncident $isoIncident): bool
    {
        return $authUser->can('Replicate:IsoIncident');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:IsoIncident');
    }
}
