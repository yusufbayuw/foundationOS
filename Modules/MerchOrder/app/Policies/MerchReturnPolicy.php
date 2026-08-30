<?php

declare(strict_types=1);

namespace Modules\MerchOrder\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\MerchOrder\Models\MerchReturn;

class MerchReturnPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:MerchReturn');
    }

    public function view(AuthUser $authUser, MerchReturn $merchReturn): bool
    {
        return $authUser->can('View:MerchReturn');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:MerchReturn');
    }

    public function update(AuthUser $authUser, MerchReturn $merchReturn): bool
    {
        return $authUser->can('Update:MerchReturn');
    }

    public function delete(AuthUser $authUser, MerchReturn $merchReturn): bool
    {
        return $authUser->can('Delete:MerchReturn');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:MerchReturn');
    }

    public function restore(AuthUser $authUser, MerchReturn $merchReturn): bool
    {
        return $authUser->can('Restore:MerchReturn');
    }

    public function forceDelete(AuthUser $authUser, MerchReturn $merchReturn): bool
    {
        return $authUser->can('ForceDelete:MerchReturn');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:MerchReturn');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:MerchReturn');
    }

    public function replicate(AuthUser $authUser, MerchReturn $merchReturn): bool
    {
        return $authUser->can('Replicate:MerchReturn');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:MerchReturn');
    }
}
