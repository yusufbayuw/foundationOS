<?php

declare(strict_types=1);

namespace Modules\Workflow\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Workflow\Models\WorkflowInstance;

class WorkflowInstancePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool { return $authUser->can('ViewAny:WorkflowInstance'); }
    public function view(AuthUser $authUser, WorkflowInstance $record): bool { return $authUser->can('View:WorkflowInstance'); }
    public function create(AuthUser $authUser): bool { return $authUser->can('Create:WorkflowInstance'); }
    public function update(AuthUser $authUser, WorkflowInstance $record): bool { return $authUser->can('Update:WorkflowInstance'); }
    public function delete(AuthUser $authUser, WorkflowInstance $record): bool { return $authUser->can('Delete:WorkflowInstance'); }
    public function deleteAny(AuthUser $authUser): bool { return $authUser->can('DeleteAny:WorkflowInstance'); }
    public function restore(AuthUser $authUser, WorkflowInstance $record): bool { return $authUser->can('Restore:WorkflowInstance'); }
    public function forceDelete(AuthUser $authUser, WorkflowInstance $record): bool { return $authUser->can('ForceDelete:WorkflowInstance'); }
    public function forceDeleteAny(AuthUser $authUser): bool { return $authUser->can('ForceDeleteAny:WorkflowInstance'); }
    public function restoreAny(AuthUser $authUser): bool { return $authUser->can('RestoreAny:WorkflowInstance'); }
    public function replicate(AuthUser $authUser, WorkflowInstance $record): bool { return $authUser->can('Replicate:WorkflowInstance'); }
    public function reorder(AuthUser $authUser): bool { return $authUser->can('Reorder:WorkflowInstance'); }
}
