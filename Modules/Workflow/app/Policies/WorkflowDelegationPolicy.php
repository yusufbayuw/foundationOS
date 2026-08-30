<?php

declare(strict_types=1);

namespace Modules\Workflow\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Workflow\Models\WorkflowDelegation;

class WorkflowDelegationPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:WorkflowDelegation');
    }

    public function view(AuthUser $authUser, WorkflowDelegation $workflowDelegation): bool
    {
        return $authUser->can('View:WorkflowDelegation');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:WorkflowDelegation');
    }

    public function update(AuthUser $authUser, WorkflowDelegation $workflowDelegation): bool
    {
        return $authUser->can('Update:WorkflowDelegation');
    }

    public function delete(AuthUser $authUser, WorkflowDelegation $workflowDelegation): bool
    {
        return $authUser->can('Delete:WorkflowDelegation');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:WorkflowDelegation');
    }

    public function restore(AuthUser $authUser, WorkflowDelegation $workflowDelegation): bool
    {
        return $authUser->can('Restore:WorkflowDelegation');
    }

    public function forceDelete(AuthUser $authUser, WorkflowDelegation $workflowDelegation): bool
    {
        return $authUser->can('ForceDelete:WorkflowDelegation');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:WorkflowDelegation');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:WorkflowDelegation');
    }

    public function replicate(AuthUser $authUser, WorkflowDelegation $workflowDelegation): bool
    {
        return $authUser->can('Replicate:WorkflowDelegation');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:WorkflowDelegation');
    }
}
