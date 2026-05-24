<?php

declare(strict_types=1);

namespace Modules\IsoCompliance\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\IsoCompliance\Models\IsoControl;

class IsoControlPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:IsoControl');
    }

    public function view(AuthUser $authUser, IsoControl $isoControl): bool
    {
        return $authUser->can('View:IsoControl');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:IsoControl');
    }

    public function update(AuthUser $authUser, IsoControl $isoControl): bool
    {
        return $authUser->can('Update:IsoControl');
    }

    public function delete(AuthUser $authUser, IsoControl $isoControl): bool
    {
        return $authUser->can('Delete:IsoControl');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:IsoControl');
    }

    public function restore(AuthUser $authUser, IsoControl $isoControl): bool
    {
        return $authUser->can('Restore:IsoControl');
    }

    public function forceDelete(AuthUser $authUser, IsoControl $isoControl): bool
    {
        return $authUser->can('ForceDelete:IsoControl');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:IsoControl');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:IsoControl');
    }

    public function replicate(AuthUser $authUser, IsoControl $isoControl): bool
    {
        return $authUser->can('Replicate:IsoControl');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:IsoControl');
    }
}
