<?php

declare(strict_types=1);

namespace Modules\Procurement\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Procurement\Models\ProcurementItem;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProcurementItemPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ProcurementItem');
    }

    public function view(AuthUser $authUser, ProcurementItem $procurementItem): bool
    {
        return $authUser->can('View:ProcurementItem');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ProcurementItem');
    }

    public function update(AuthUser $authUser, ProcurementItem $procurementItem): bool
    {
        return $authUser->can('Update:ProcurementItem');
    }

    public function delete(AuthUser $authUser, ProcurementItem $procurementItem): bool
    {
        return $authUser->can('Delete:ProcurementItem');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ProcurementItem');
    }

    public function restore(AuthUser $authUser, ProcurementItem $procurementItem): bool
    {
        return $authUser->can('Restore:ProcurementItem');
    }

    public function forceDelete(AuthUser $authUser, ProcurementItem $procurementItem): bool
    {
        return $authUser->can('ForceDelete:ProcurementItem');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ProcurementItem');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ProcurementItem');
    }

    public function replicate(AuthUser $authUser, ProcurementItem $procurementItem): bool
    {
        return $authUser->can('Replicate:ProcurementItem');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ProcurementItem');
    }

}