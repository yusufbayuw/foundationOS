<?php

declare(strict_types=1);

namespace Modules\Campus\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Campus\Models\MbkmActivity;

class MbkmActivityPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:MbkmActivity');
    }

    public function view(AuthUser $authUser, MbkmActivity $mbkmActivity): bool
    {
        return $authUser->can('View:MbkmActivity');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:MbkmActivity');
    }

    public function update(AuthUser $authUser, MbkmActivity $mbkmActivity): bool
    {
        return $authUser->can('Update:MbkmActivity');
    }

    public function delete(AuthUser $authUser, MbkmActivity $mbkmActivity): bool
    {
        return $authUser->can('Delete:MbkmActivity');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:MbkmActivity');
    }

    public function restore(AuthUser $authUser, MbkmActivity $mbkmActivity): bool
    {
        return $authUser->can('Restore:MbkmActivity');
    }

    public function forceDelete(AuthUser $authUser, MbkmActivity $mbkmActivity): bool
    {
        return $authUser->can('ForceDelete:MbkmActivity');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:MbkmActivity');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:MbkmActivity');
    }

    public function replicate(AuthUser $authUser, MbkmActivity $mbkmActivity): bool
    {
        return $authUser->can('Replicate:MbkmActivity');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:MbkmActivity');
    }
}
