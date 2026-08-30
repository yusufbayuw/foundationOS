<?php

declare(strict_types=1);

namespace Modules\ItOps\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\ItOps\Models\UserAccount;

class UserAccountPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:UserAccount');
    }

    public function view(AuthUser $authUser, UserAccount $userAccount): bool
    {
        return $authUser->can('View:UserAccount');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:UserAccount');
    }

    public function update(AuthUser $authUser, UserAccount $userAccount): bool
    {
        return $authUser->can('Update:UserAccount');
    }

    public function delete(AuthUser $authUser, UserAccount $userAccount): bool
    {
        return $authUser->can('Delete:UserAccount');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:UserAccount');
    }

    public function restore(AuthUser $authUser, UserAccount $userAccount): bool
    {
        return $authUser->can('Restore:UserAccount');
    }

    public function forceDelete(AuthUser $authUser, UserAccount $userAccount): bool
    {
        return $authUser->can('ForceDelete:UserAccount');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:UserAccount');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:UserAccount');
    }

    public function replicate(AuthUser $authUser, UserAccount $userAccount): bool
    {
        return $authUser->can('Replicate:UserAccount');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:UserAccount');
    }
}
