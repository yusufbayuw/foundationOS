<?php

declare(strict_types=1);

namespace Modules\Printing\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Printing\Models\PrintOrderItem;

class PrintOrderItemPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PrintOrderItem');
    }

    public function view(AuthUser $authUser, PrintOrderItem $printOrderItem): bool
    {
        return $authUser->can('View:PrintOrderItem');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PrintOrderItem');
    }

    public function update(AuthUser $authUser, PrintOrderItem $printOrderItem): bool
    {
        return $authUser->can('Update:PrintOrderItem');
    }

    public function delete(AuthUser $authUser, PrintOrderItem $printOrderItem): bool
    {
        return $authUser->can('Delete:PrintOrderItem');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PrintOrderItem');
    }

    public function restore(AuthUser $authUser, PrintOrderItem $printOrderItem): bool
    {
        return $authUser->can('Restore:PrintOrderItem');
    }

    public function forceDelete(AuthUser $authUser, PrintOrderItem $printOrderItem): bool
    {
        return $authUser->can('ForceDelete:PrintOrderItem');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PrintOrderItem');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PrintOrderItem');
    }

    public function replicate(AuthUser $authUser, PrintOrderItem $printOrderItem): bool
    {
        return $authUser->can('Replicate:PrintOrderItem');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PrintOrderItem');
    }
}
