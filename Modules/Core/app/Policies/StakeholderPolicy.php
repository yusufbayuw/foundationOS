<?php

declare(strict_types=1);

namespace Modules\Core\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Core\Models\Stakeholder;

class StakeholderPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Stakeholder');
    }

    public function view(AuthUser $authUser, Stakeholder $stakeholder): bool
    {
        return $authUser->can('View:Stakeholder');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Stakeholder');
    }

    public function update(AuthUser $authUser, Stakeholder $stakeholder): bool
    {
        return $authUser->can('Update:Stakeholder');
    }

    public function delete(AuthUser $authUser, Stakeholder $stakeholder): bool
    {
        return $authUser->can('Delete:Stakeholder');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Stakeholder');
    }

    public function restore(AuthUser $authUser, Stakeholder $stakeholder): bool
    {
        return $authUser->can('Restore:Stakeholder');
    }

    public function forceDelete(AuthUser $authUser, Stakeholder $stakeholder): bool
    {
        return $authUser->can('ForceDelete:Stakeholder');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Stakeholder');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Stakeholder');
    }

    public function replicate(AuthUser $authUser, Stakeholder $stakeholder): bool
    {
        return $authUser->can('Replicate:Stakeholder');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Stakeholder');
    }
}
