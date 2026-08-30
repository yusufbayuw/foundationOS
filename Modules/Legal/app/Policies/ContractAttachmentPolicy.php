<?php

declare(strict_types=1);

namespace Modules\Legal\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Legal\Models\ContractAttachment;

class ContractAttachmentPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ContractAttachment');
    }

    public function view(AuthUser $authUser, ContractAttachment $contractAttachment): bool
    {
        return $authUser->can('View:ContractAttachment');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ContractAttachment');
    }

    public function update(AuthUser $authUser, ContractAttachment $contractAttachment): bool
    {
        return $authUser->can('Update:ContractAttachment');
    }

    public function delete(AuthUser $authUser, ContractAttachment $contractAttachment): bool
    {
        return $authUser->can('Delete:ContractAttachment');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ContractAttachment');
    }

    public function restore(AuthUser $authUser, ContractAttachment $contractAttachment): bool
    {
        return $authUser->can('Restore:ContractAttachment');
    }

    public function forceDelete(AuthUser $authUser, ContractAttachment $contractAttachment): bool
    {
        return $authUser->can('ForceDelete:ContractAttachment');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ContractAttachment');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ContractAttachment');
    }

    public function replicate(AuthUser $authUser, ContractAttachment $contractAttachment): bool
    {
        return $authUser->can('Replicate:ContractAttachment');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ContractAttachment');
    }
}
