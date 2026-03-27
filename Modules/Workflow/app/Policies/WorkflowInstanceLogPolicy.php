<?php

declare(strict_types=1);

namespace Modules\Workflow\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Workflow\Models\WorkflowInstanceLog;

class WorkflowInstanceLogPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool { return $authUser->can('ViewAny:WorkflowInstanceLog'); }
    public function view(AuthUser $authUser, WorkflowInstanceLog $record): bool { return $authUser->can('View:WorkflowInstanceLog'); }
    public function create(AuthUser $authUser): bool { return $authUser->can('Create:WorkflowInstanceLog'); }
    public function update(AuthUser $authUser, WorkflowInstanceLog $record): bool { return $authUser->can('Update:WorkflowInstanceLog'); }
    public function delete(AuthUser $authUser, WorkflowInstanceLog $record): bool { return $authUser->can('Delete:WorkflowInstanceLog'); }
    public function deleteAny(AuthUser $authUser): bool { return $authUser->can('DeleteAny:WorkflowInstanceLog'); }
    public function restore(AuthUser $authUser, WorkflowInstanceLog $record): bool { return $authUser->can('Restore:WorkflowInstanceLog'); }
    public function forceDelete(AuthUser $authUser, WorkflowInstanceLog $record): bool { return $authUser->can('ForceDelete:WorkflowInstanceLog'); }
    public function forceDeleteAny(AuthUser $authUser): bool { return $authUser->can('ForceDeleteAny:WorkflowInstanceLog'); }
    public function restoreAny(AuthUser $authUser): bool { return $authUser->can('RestoreAny:WorkflowInstanceLog'); }
    public function replicate(AuthUser $authUser, WorkflowInstanceLog $record): bool { return $authUser->can('Replicate:WorkflowInstanceLog'); }
    public function reorder(AuthUser $authUser): bool { return $authUser->can('Reorder:WorkflowInstanceLog'); }
}
