<?php

declare(strict_types=1);

namespace Modules\Printing\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Printing\Models\PrintOrder;

class PrintOrderPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PrintOrder');
    }

    public function view(AuthUser $authUser, PrintOrder $printOrder): bool
    {
        return $authUser->can('View:PrintOrder');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PrintOrder');
    }

    public function update(AuthUser $authUser, PrintOrder $printOrder): bool
    {
        return $authUser->can('Update:PrintOrder');
    }

    public function delete(AuthUser $authUser, PrintOrder $printOrder): bool
    {
        return $authUser->can('Delete:PrintOrder');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PrintOrder');
    }

    public function restore(AuthUser $authUser, PrintOrder $printOrder): bool
    {
        return $authUser->can('Restore:PrintOrder');
    }

    public function forceDelete(AuthUser $authUser, PrintOrder $printOrder): bool
    {
        return $authUser->can('ForceDelete:PrintOrder');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PrintOrder');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PrintOrder');
    }

    public function replicate(AuthUser $authUser, PrintOrder $printOrder): bool
    {
        return $authUser->can('Replicate:PrintOrder');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PrintOrder');
    }
}
