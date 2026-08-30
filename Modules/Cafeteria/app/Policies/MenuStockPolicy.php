<?php

declare(strict_types=1);

namespace Modules\Cafeteria\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Cafeteria\Models\MenuStock;

class MenuStockPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:MenuStock');
    }

    public function view(AuthUser $authUser, MenuStock $menuStock): bool
    {
        return $authUser->can('View:MenuStock');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:MenuStock');
    }

    public function update(AuthUser $authUser, MenuStock $menuStock): bool
    {
        return $authUser->can('Update:MenuStock');
    }

    public function delete(AuthUser $authUser, MenuStock $menuStock): bool
    {
        return $authUser->can('Delete:MenuStock');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:MenuStock');
    }

    public function restore(AuthUser $authUser, MenuStock $menuStock): bool
    {
        return $authUser->can('Restore:MenuStock');
    }

    public function forceDelete(AuthUser $authUser, MenuStock $menuStock): bool
    {
        return $authUser->can('ForceDelete:MenuStock');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:MenuStock');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:MenuStock');
    }

    public function replicate(AuthUser $authUser, MenuStock $menuStock): bool
    {
        return $authUser->can('Replicate:MenuStock');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:MenuStock');
    }
}
