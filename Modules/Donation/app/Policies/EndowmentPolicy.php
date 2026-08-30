<?php

declare(strict_types=1);

namespace Modules\Donation\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Donation\Models\Endowment;

class EndowmentPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Endowment');
    }

    public function view(AuthUser $authUser, Endowment $endowment): bool
    {
        return $authUser->can('View:Endowment');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Endowment');
    }

    public function update(AuthUser $authUser, Endowment $endowment): bool
    {
        return $authUser->can('Update:Endowment');
    }

    public function delete(AuthUser $authUser, Endowment $endowment): bool
    {
        return $authUser->can('Delete:Endowment');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Endowment');
    }

    public function restore(AuthUser $authUser, Endowment $endowment): bool
    {
        return $authUser->can('Restore:Endowment');
    }

    public function forceDelete(AuthUser $authUser, Endowment $endowment): bool
    {
        return $authUser->can('ForceDelete:Endowment');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Endowment');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Endowment');
    }

    public function replicate(AuthUser $authUser, Endowment $endowment): bool
    {
        return $authUser->can('Replicate:Endowment');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Endowment');
    }
}
