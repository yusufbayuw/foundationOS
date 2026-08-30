<?php

declare(strict_types=1);

namespace Modules\Sales\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Sales\Models\CooperativeSaving;

class CooperativeSavingPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CooperativeSaving');
    }

    public function view(AuthUser $authUser, CooperativeSaving $cooperativeSaving): bool
    {
        return $authUser->can('View:CooperativeSaving');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CooperativeSaving');
    }

    public function update(AuthUser $authUser, CooperativeSaving $cooperativeSaving): bool
    {
        return $authUser->can('Update:CooperativeSaving');
    }

    public function delete(AuthUser $authUser, CooperativeSaving $cooperativeSaving): bool
    {
        return $authUser->can('Delete:CooperativeSaving');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:CooperativeSaving');
    }

    public function restore(AuthUser $authUser, CooperativeSaving $cooperativeSaving): bool
    {
        return $authUser->can('Restore:CooperativeSaving');
    }

    public function forceDelete(AuthUser $authUser, CooperativeSaving $cooperativeSaving): bool
    {
        return $authUser->can('ForceDelete:CooperativeSaving');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CooperativeSaving');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CooperativeSaving');
    }

    public function replicate(AuthUser $authUser, CooperativeSaving $cooperativeSaving): bool
    {
        return $authUser->can('Replicate:CooperativeSaving');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CooperativeSaving');
    }
}
