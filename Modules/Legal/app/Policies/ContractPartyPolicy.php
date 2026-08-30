<?php

declare(strict_types=1);

namespace Modules\Legal\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Legal\Models\ContractParty;

class ContractPartyPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ContractParty');
    }

    public function view(AuthUser $authUser, ContractParty $contractParty): bool
    {
        return $authUser->can('View:ContractParty');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ContractParty');
    }

    public function update(AuthUser $authUser, ContractParty $contractParty): bool
    {
        return $authUser->can('Update:ContractParty');
    }

    public function delete(AuthUser $authUser, ContractParty $contractParty): bool
    {
        return $authUser->can('Delete:ContractParty');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ContractParty');
    }

    public function restore(AuthUser $authUser, ContractParty $contractParty): bool
    {
        return $authUser->can('Restore:ContractParty');
    }

    public function forceDelete(AuthUser $authUser, ContractParty $contractParty): bool
    {
        return $authUser->can('ForceDelete:ContractParty');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ContractParty');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ContractParty');
    }

    public function replicate(AuthUser $authUser, ContractParty $contractParty): bool
    {
        return $authUser->can('Replicate:ContractParty');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ContractParty');
    }
}
