<?php

declare(strict_types=1);

namespace Modules\InternalAudit\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\InternalAudit\Models\PreventiveAction;

class PreventiveActionPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PreventiveAction');
    }

    public function view(AuthUser $authUser, PreventiveAction $preventiveAction): bool
    {
        return $authUser->can('View:PreventiveAction');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PreventiveAction');
    }

    public function update(AuthUser $authUser, PreventiveAction $preventiveAction): bool
    {
        return $authUser->can('Update:PreventiveAction');
    }

    public function delete(AuthUser $authUser, PreventiveAction $preventiveAction): bool
    {
        return $authUser->can('Delete:PreventiveAction');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PreventiveAction');
    }

    public function restore(AuthUser $authUser, PreventiveAction $preventiveAction): bool
    {
        return $authUser->can('Restore:PreventiveAction');
    }

    public function forceDelete(AuthUser $authUser, PreventiveAction $preventiveAction): bool
    {
        return $authUser->can('ForceDelete:PreventiveAction');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PreventiveAction');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PreventiveAction');
    }

    public function replicate(AuthUser $authUser, PreventiveAction $preventiveAction): bool
    {
        return $authUser->can('Replicate:PreventiveAction');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PreventiveAction');
    }
}
