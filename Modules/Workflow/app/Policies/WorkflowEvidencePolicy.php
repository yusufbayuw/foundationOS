<?php

declare(strict_types=1);

namespace Modules\Workflow\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Workflow\Models\WorkflowEvidence;

class WorkflowEvidencePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:WorkflowEvidence');
    }

    public function view(AuthUser $authUser, WorkflowEvidence $workflowEvidence): bool
    {
        return $authUser->can('View:WorkflowEvidence');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:WorkflowEvidence');
    }

    public function update(AuthUser $authUser, WorkflowEvidence $workflowEvidence): bool
    {
        return $authUser->can('Update:WorkflowEvidence');
    }

    public function delete(AuthUser $authUser, WorkflowEvidence $workflowEvidence): bool
    {
        return $authUser->can('Delete:WorkflowEvidence');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:WorkflowEvidence');
    }

    public function restore(AuthUser $authUser, WorkflowEvidence $workflowEvidence): bool
    {
        return $authUser->can('Restore:WorkflowEvidence');
    }

    public function forceDelete(AuthUser $authUser, WorkflowEvidence $workflowEvidence): bool
    {
        return $authUser->can('ForceDelete:WorkflowEvidence');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:WorkflowEvidence');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:WorkflowEvidence');
    }

    public function replicate(AuthUser $authUser, WorkflowEvidence $workflowEvidence): bool
    {
        return $authUser->can('Replicate:WorkflowEvidence');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:WorkflowEvidence');
    }
}
