<?php

declare(strict_types=1);

namespace Modules\IsoCompliance\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\IsoCompliance\Models\IsoPolicy;

class IsoPolicyPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:IsoPolicy');
    }

    public function view(AuthUser $authUser, IsoPolicy $isoPolicy): bool
    {
        return $authUser->can('View:IsoPolicy');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:IsoPolicy');
    }

    public function update(AuthUser $authUser, IsoPolicy $isoPolicy): bool
    {
        return $authUser->can('Update:IsoPolicy');
    }

    public function delete(AuthUser $authUser, IsoPolicy $isoPolicy): bool
    {
        return $authUser->can('Delete:IsoPolicy');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:IsoPolicy');
    }

    public function restore(AuthUser $authUser, IsoPolicy $isoPolicy): bool
    {
        return $authUser->can('Restore:IsoPolicy');
    }

    public function forceDelete(AuthUser $authUser, IsoPolicy $isoPolicy): bool
    {
        return $authUser->can('ForceDelete:IsoPolicy');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:IsoPolicy');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:IsoPolicy');
    }

    public function replicate(AuthUser $authUser, IsoPolicy $isoPolicy): bool
    {
        return $authUser->can('Replicate:IsoPolicy');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:IsoPolicy');
    }
}
