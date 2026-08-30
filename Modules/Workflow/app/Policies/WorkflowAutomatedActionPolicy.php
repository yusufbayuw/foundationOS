<?php

declare(strict_types=1);

namespace Modules\Workflow\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Workflow\Models\WorkflowAutomatedAction;

class WorkflowAutomatedActionPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:WorkflowAutomatedAction');
    }

    public function view(AuthUser $authUser, WorkflowAutomatedAction $workflowAutomatedAction): bool
    {
        return $authUser->can('View:WorkflowAutomatedAction');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:WorkflowAutomatedAction');
    }

    public function update(AuthUser $authUser, WorkflowAutomatedAction $workflowAutomatedAction): bool
    {
        return $authUser->can('Update:WorkflowAutomatedAction');
    }

    public function delete(AuthUser $authUser, WorkflowAutomatedAction $workflowAutomatedAction): bool
    {
        return $authUser->can('Delete:WorkflowAutomatedAction');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:WorkflowAutomatedAction');
    }

    public function restore(AuthUser $authUser, WorkflowAutomatedAction $workflowAutomatedAction): bool
    {
        return $authUser->can('Restore:WorkflowAutomatedAction');
    }

    public function forceDelete(AuthUser $authUser, WorkflowAutomatedAction $workflowAutomatedAction): bool
    {
        return $authUser->can('ForceDelete:WorkflowAutomatedAction');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:WorkflowAutomatedAction');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:WorkflowAutomatedAction');
    }

    public function replicate(AuthUser $authUser, WorkflowAutomatedAction $workflowAutomatedAction): bool
    {
        return $authUser->can('Replicate:WorkflowAutomatedAction');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:WorkflowAutomatedAction');
    }
}
