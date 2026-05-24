<?php

declare(strict_types=1);

namespace Modules\Boarding\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Boarding\Models\Dormitory;

class DormitoryPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Dormitory');
    }

    public function view(AuthUser $authUser, Dormitory $dormitory): bool
    {
        return $authUser->can('View:Dormitory');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Dormitory');
    }

    public function update(AuthUser $authUser, Dormitory $dormitory): bool
    {
        return $authUser->can('Update:Dormitory');
    }

    public function delete(AuthUser $authUser, Dormitory $dormitory): bool
    {
        return $authUser->can('Delete:Dormitory');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Dormitory');
    }

    public function restore(AuthUser $authUser, Dormitory $dormitory): bool
    {
        return $authUser->can('Restore:Dormitory');
    }

    public function forceDelete(AuthUser $authUser, Dormitory $dormitory): bool
    {
        return $authUser->can('ForceDelete:Dormitory');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Dormitory');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Dormitory');
    }

    public function replicate(AuthUser $authUser, Dormitory $dormitory): bool
    {
        return $authUser->can('Replicate:Dormitory');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Dormitory');
    }
}
