<?php

declare(strict_types=1);

namespace Modules\Enrollment\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Enrollment\Models\LeadActivity;

class LeadActivityPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:LeadActivity');
    }

    public function view(AuthUser $authUser, LeadActivity $leadActivity): bool
    {
        return $authUser->can('View:LeadActivity');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:LeadActivity');
    }

    public function update(AuthUser $authUser, LeadActivity $leadActivity): bool
    {
        return $authUser->can('Update:LeadActivity');
    }

    public function delete(AuthUser $authUser, LeadActivity $leadActivity): bool
    {
        return $authUser->can('Delete:LeadActivity');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:LeadActivity');
    }

    public function restore(AuthUser $authUser, LeadActivity $leadActivity): bool
    {
        return $authUser->can('Restore:LeadActivity');
    }

    public function forceDelete(AuthUser $authUser, LeadActivity $leadActivity): bool
    {
        return $authUser->can('ForceDelete:LeadActivity');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:LeadActivity');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:LeadActivity');
    }

    public function replicate(AuthUser $authUser, LeadActivity $leadActivity): bool
    {
        return $authUser->can('Replicate:LeadActivity');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:LeadActivity');
    }
}
