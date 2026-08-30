<?php

declare(strict_types=1);

namespace Modules\Workflow\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Workflow\Models\WorkflowStepBranch;

class WorkflowStepBranchPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:WorkflowStepBranch');
    }

    public function view(AuthUser $authUser, WorkflowStepBranch $workflowStepBranch): bool
    {
        return $authUser->can('View:WorkflowStepBranch');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:WorkflowStepBranch');
    }

    public function update(AuthUser $authUser, WorkflowStepBranch $workflowStepBranch): bool
    {
        return $authUser->can('Update:WorkflowStepBranch');
    }

    public function delete(AuthUser $authUser, WorkflowStepBranch $workflowStepBranch): bool
    {
        return $authUser->can('Delete:WorkflowStepBranch');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:WorkflowStepBranch');
    }

    public function restore(AuthUser $authUser, WorkflowStepBranch $workflowStepBranch): bool
    {
        return $authUser->can('Restore:WorkflowStepBranch');
    }

    public function forceDelete(AuthUser $authUser, WorkflowStepBranch $workflowStepBranch): bool
    {
        return $authUser->can('ForceDelete:WorkflowStepBranch');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:WorkflowStepBranch');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:WorkflowStepBranch');
    }

    public function replicate(AuthUser $authUser, WorkflowStepBranch $workflowStepBranch): bool
    {
        return $authUser->can('Replicate:WorkflowStepBranch');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:WorkflowStepBranch');
    }
}
