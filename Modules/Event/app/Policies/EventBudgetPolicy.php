<?php

declare(strict_types=1);

namespace Modules\Event\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Event\Models\EventBudget;

class EventBudgetPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:EventBudget');
    }

    public function view(AuthUser $authUser, EventBudget $eventBudget): bool
    {
        return $authUser->can('View:EventBudget');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:EventBudget');
    }

    public function update(AuthUser $authUser, EventBudget $eventBudget): bool
    {
        return $authUser->can('Update:EventBudget');
    }

    public function delete(AuthUser $authUser, EventBudget $eventBudget): bool
    {
        return $authUser->can('Delete:EventBudget');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:EventBudget');
    }

    public function restore(AuthUser $authUser, EventBudget $eventBudget): bool
    {
        return $authUser->can('Restore:EventBudget');
    }

    public function forceDelete(AuthUser $authUser, EventBudget $eventBudget): bool
    {
        return $authUser->can('ForceDelete:EventBudget');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:EventBudget');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:EventBudget');
    }

    public function replicate(AuthUser $authUser, EventBudget $eventBudget): bool
    {
        return $authUser->can('Replicate:EventBudget');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:EventBudget');
    }
}
