<?php

declare(strict_types=1);

namespace Modules\Workflow\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Workflow\Models\WorkflowStep;

class WorkflowStepPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool { return $authUser->can('ViewAny:WorkflowStep'); }
    public function view(AuthUser $authUser, WorkflowStep $record): bool { return $authUser->can('View:WorkflowStep'); }
    public function create(AuthUser $authUser): bool { return $authUser->can('Create:WorkflowStep'); }
    public function update(AuthUser $authUser, WorkflowStep $record): bool { return $authUser->can('Update:WorkflowStep'); }
    public function delete(AuthUser $authUser, WorkflowStep $record): bool { return $authUser->can('Delete:WorkflowStep'); }
    public function deleteAny(AuthUser $authUser): bool { return $authUser->can('DeleteAny:WorkflowStep'); }
    public function restore(AuthUser $authUser, WorkflowStep $record): bool { return $authUser->can('Restore:WorkflowStep'); }
    public function forceDelete(AuthUser $authUser, WorkflowStep $record): bool { return $authUser->can('ForceDelete:WorkflowStep'); }
    public function forceDeleteAny(AuthUser $authUser): bool { return $authUser->can('ForceDeleteAny:WorkflowStep'); }
    public function restoreAny(AuthUser $authUser): bool { return $authUser->can('RestoreAny:WorkflowStep'); }
    public function replicate(AuthUser $authUser, WorkflowStep $record): bool { return $authUser->can('Replicate:WorkflowStep'); }
    public function reorder(AuthUser $authUser): bool { return $authUser->can('Reorder:WorkflowStep'); }
}
