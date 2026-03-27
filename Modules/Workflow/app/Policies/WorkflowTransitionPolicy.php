<?php

declare(strict_types=1);

namespace Modules\Workflow\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Workflow\Models\WorkflowTransition;

class WorkflowTransitionPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool { return $authUser->can('ViewAny:WorkflowTransition'); }
    public function view(AuthUser $authUser, WorkflowTransition $record): bool { return $authUser->can('View:WorkflowTransition'); }
    public function create(AuthUser $authUser): bool { return $authUser->can('Create:WorkflowTransition'); }
    public function update(AuthUser $authUser, WorkflowTransition $record): bool { return $authUser->can('Update:WorkflowTransition'); }
    public function delete(AuthUser $authUser, WorkflowTransition $record): bool { return $authUser->can('Delete:WorkflowTransition'); }
    public function deleteAny(AuthUser $authUser): bool { return $authUser->can('DeleteAny:WorkflowTransition'); }
    public function restore(AuthUser $authUser, WorkflowTransition $record): bool { return $authUser->can('Restore:WorkflowTransition'); }
    public function forceDelete(AuthUser $authUser, WorkflowTransition $record): bool { return $authUser->can('ForceDelete:WorkflowTransition'); }
    public function forceDeleteAny(AuthUser $authUser): bool { return $authUser->can('ForceDeleteAny:WorkflowTransition'); }
    public function restoreAny(AuthUser $authUser): bool { return $authUser->can('RestoreAny:WorkflowTransition'); }
    public function replicate(AuthUser $authUser, WorkflowTransition $record): bool { return $authUser->can('Replicate:WorkflowTransition'); }
    public function reorder(AuthUser $authUser): bool { return $authUser->can('Reorder:WorkflowTransition'); }
}
