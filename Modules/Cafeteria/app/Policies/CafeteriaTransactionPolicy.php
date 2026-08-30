<?php

declare(strict_types=1);

namespace Modules\Cafeteria\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Cafeteria\Models\CafeteriaTransaction;

class CafeteriaTransactionPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CafeteriaTransaction');
    }

    public function view(AuthUser $authUser, CafeteriaTransaction $cafeteriaTransaction): bool
    {
        return $authUser->can('View:CafeteriaTransaction');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CafeteriaTransaction');
    }

    public function update(AuthUser $authUser, CafeteriaTransaction $cafeteriaTransaction): bool
    {
        return $authUser->can('Update:CafeteriaTransaction');
    }

    public function delete(AuthUser $authUser, CafeteriaTransaction $cafeteriaTransaction): bool
    {
        return $authUser->can('Delete:CafeteriaTransaction');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:CafeteriaTransaction');
    }

    public function restore(AuthUser $authUser, CafeteriaTransaction $cafeteriaTransaction): bool
    {
        return $authUser->can('Restore:CafeteriaTransaction');
    }

    public function forceDelete(AuthUser $authUser, CafeteriaTransaction $cafeteriaTransaction): bool
    {
        return $authUser->can('ForceDelete:CafeteriaTransaction');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CafeteriaTransaction');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CafeteriaTransaction');
    }

    public function replicate(AuthUser $authUser, CafeteriaTransaction $cafeteriaTransaction): bool
    {
        return $authUser->can('Replicate:CafeteriaTransaction');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CafeteriaTransaction');
    }
}
