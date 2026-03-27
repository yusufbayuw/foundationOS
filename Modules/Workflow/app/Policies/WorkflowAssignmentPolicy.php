<?php

declare(strict_types=1);

namespace Modules\Workflow\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Workflow\Models\WorkflowAssignment;

class WorkflowAssignmentPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool { return $authUser->can('ViewAny:WorkflowAssignment'); }
    public function view(AuthUser $authUser, WorkflowAssignment $record): bool { return $authUser->can('View:WorkflowAssignment'); }
    public function create(AuthUser $authUser): bool { return $authUser->can('Create:WorkflowAssignment'); }
    public function update(AuthUser $authUser, WorkflowAssignment $record): bool { return $authUser->can('Update:WorkflowAssignment'); }
    public function delete(AuthUser $authUser, WorkflowAssignment $record): bool { return $authUser->can('Delete:WorkflowAssignment'); }
    public function deleteAny(AuthUser $authUser): bool { return $authUser->can('DeleteAny:WorkflowAssignment'); }
    public function restore(AuthUser $authUser, WorkflowAssignment $record): bool { return $authUser->can('Restore:WorkflowAssignment'); }
    public function forceDelete(AuthUser $authUser, WorkflowAssignment $record): bool { return $authUser->can('ForceDelete:WorkflowAssignment'); }
    public function forceDeleteAny(AuthUser $authUser): bool { return $authUser->can('ForceDeleteAny:WorkflowAssignment'); }
    public function restoreAny(AuthUser $authUser): bool { return $authUser->can('RestoreAny:WorkflowAssignment'); }
    public function replicate(AuthUser $authUser, WorkflowAssignment $record): bool { return $authUser->can('Replicate:WorkflowAssignment'); }
    public function reorder(AuthUser $authUser): bool { return $authUser->can('Reorder:WorkflowAssignment'); }
}
