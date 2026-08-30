<?php

declare(strict_types=1);

namespace Modules\Transport\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Transport\Models\StudentShuttleSubscription;

class StudentShuttleSubscriptionPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:StudentShuttleSubscription');
    }

    public function view(AuthUser $authUser, StudentShuttleSubscription $studentShuttleSubscription): bool
    {
        return $authUser->can('View:StudentShuttleSubscription');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:StudentShuttleSubscription');
    }

    public function update(AuthUser $authUser, StudentShuttleSubscription $studentShuttleSubscription): bool
    {
        return $authUser->can('Update:StudentShuttleSubscription');
    }

    public function delete(AuthUser $authUser, StudentShuttleSubscription $studentShuttleSubscription): bool
    {
        return $authUser->can('Delete:StudentShuttleSubscription');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:StudentShuttleSubscription');
    }

    public function restore(AuthUser $authUser, StudentShuttleSubscription $studentShuttleSubscription): bool
    {
        return $authUser->can('Restore:StudentShuttleSubscription');
    }

    public function forceDelete(AuthUser $authUser, StudentShuttleSubscription $studentShuttleSubscription): bool
    {
        return $authUser->can('ForceDelete:StudentShuttleSubscription');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:StudentShuttleSubscription');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:StudentShuttleSubscription');
    }

    public function replicate(AuthUser $authUser, StudentShuttleSubscription $studentShuttleSubscription): bool
    {
        return $authUser->can('Replicate:StudentShuttleSubscription');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:StudentShuttleSubscription');
    }
}
