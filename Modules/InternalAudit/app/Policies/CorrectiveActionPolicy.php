<?php

declare(strict_types=1);

namespace Modules\InternalAudit\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\InternalAudit\Models\CorrectiveAction;

class CorrectiveActionPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CorrectiveAction');
    }

    public function view(AuthUser $authUser, CorrectiveAction $correctiveAction): bool
    {
        return $authUser->can('View:CorrectiveAction');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CorrectiveAction');
    }

    public function update(AuthUser $authUser, CorrectiveAction $correctiveAction): bool
    {
        return $authUser->can('Update:CorrectiveAction');
    }

    public function delete(AuthUser $authUser, CorrectiveAction $correctiveAction): bool
    {
        return $authUser->can('Delete:CorrectiveAction');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:CorrectiveAction');
    }

    public function restore(AuthUser $authUser, CorrectiveAction $correctiveAction): bool
    {
        return $authUser->can('Restore:CorrectiveAction');
    }

    public function forceDelete(AuthUser $authUser, CorrectiveAction $correctiveAction): bool
    {
        return $authUser->can('ForceDelete:CorrectiveAction');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CorrectiveAction');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CorrectiveAction');
    }

    public function replicate(AuthUser $authUser, CorrectiveAction $correctiveAction): bool
    {
        return $authUser->can('Replicate:CorrectiveAction');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CorrectiveAction');
    }
}
