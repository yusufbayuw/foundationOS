<?php

declare(strict_types=1);

namespace Modules\Campus\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Campus\Models\FeederLog;
use Illuminate\Auth\Access\HandlesAuthorization;

class FeederLogPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:FeederLog');
    }

    public function view(AuthUser $authUser, FeederLog $feederLog): bool
    {
        return $authUser->can('View:FeederLog');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:FeederLog');
    }

    public function update(AuthUser $authUser, FeederLog $feederLog): bool
    {
        return $authUser->can('Update:FeederLog');
    }

    public function delete(AuthUser $authUser, FeederLog $feederLog): bool
    {
        return $authUser->can('Delete:FeederLog');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:FeederLog');
    }

    public function restore(AuthUser $authUser, FeederLog $feederLog): bool
    {
        return $authUser->can('Restore:FeederLog');
    }

    public function forceDelete(AuthUser $authUser, FeederLog $feederLog): bool
    {
        return $authUser->can('ForceDelete:FeederLog');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:FeederLog');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:FeederLog');
    }

    public function replicate(AuthUser $authUser, FeederLog $feederLog): bool
    {
        return $authUser->can('Replicate:FeederLog');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:FeederLog');
    }

}