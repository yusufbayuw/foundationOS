<?php

declare(strict_types=1);

namespace Modules\MerchOrder\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\MerchOrder\Models\MerchOrder;

class MerchOrderPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:MerchOrder');
    }

    public function view(AuthUser $authUser, MerchOrder $merchOrder): bool
    {
        return $authUser->can('View:MerchOrder');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:MerchOrder');
    }

    public function update(AuthUser $authUser, MerchOrder $merchOrder): bool
    {
        return $authUser->can('Update:MerchOrder');
    }

    public function delete(AuthUser $authUser, MerchOrder $merchOrder): bool
    {
        return $authUser->can('Delete:MerchOrder');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:MerchOrder');
    }

    public function restore(AuthUser $authUser, MerchOrder $merchOrder): bool
    {
        return $authUser->can('Restore:MerchOrder');
    }

    public function forceDelete(AuthUser $authUser, MerchOrder $merchOrder): bool
    {
        return $authUser->can('ForceDelete:MerchOrder');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:MerchOrder');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:MerchOrder');
    }

    public function replicate(AuthUser $authUser, MerchOrder $merchOrder): bool
    {
        return $authUser->can('Replicate:MerchOrder');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:MerchOrder');
    }
}
