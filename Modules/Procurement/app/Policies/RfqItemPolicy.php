<?php

declare(strict_types=1);

namespace Modules\Procurement\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Procurement\Models\RfqItem;

class RfqItemPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:RfqItem');
    }

    public function view(AuthUser $authUser, RfqItem $rfqItem): bool
    {
        return $authUser->can('View:RfqItem');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:RfqItem');
    }

    public function update(AuthUser $authUser, RfqItem $rfqItem): bool
    {
        return $authUser->can('Update:RfqItem');
    }

    public function delete(AuthUser $authUser, RfqItem $rfqItem): bool
    {
        return $authUser->can('Delete:RfqItem');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:RfqItem');
    }

    public function restore(AuthUser $authUser, RfqItem $rfqItem): bool
    {
        return $authUser->can('Restore:RfqItem');
    }

    public function forceDelete(AuthUser $authUser, RfqItem $rfqItem): bool
    {
        return $authUser->can('ForceDelete:RfqItem');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:RfqItem');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:RfqItem');
    }

    public function replicate(AuthUser $authUser, RfqItem $rfqItem): bool
    {
        return $authUser->can('Replicate:RfqItem');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:RfqItem');
    }
}
