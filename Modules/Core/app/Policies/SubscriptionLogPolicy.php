<?php

declare(strict_types=1);

namespace Modules\Core\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Core\Models\SubscriptionLog;
use Illuminate\Auth\Access\HandlesAuthorization;

class SubscriptionLogPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SubscriptionLog');
    }

    public function view(AuthUser $authUser, SubscriptionLog $subscriptionLog): bool
    {
        return $authUser->can('View:SubscriptionLog');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SubscriptionLog');
    }

    public function update(AuthUser $authUser, SubscriptionLog $subscriptionLog): bool
    {
        return $authUser->can('Update:SubscriptionLog');
    }

    public function delete(AuthUser $authUser, SubscriptionLog $subscriptionLog): bool
    {
        return $authUser->can('Delete:SubscriptionLog');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:SubscriptionLog');
    }

    public function restore(AuthUser $authUser, SubscriptionLog $subscriptionLog): bool
    {
        return $authUser->can('Restore:SubscriptionLog');
    }

    public function forceDelete(AuthUser $authUser, SubscriptionLog $subscriptionLog): bool
    {
        return $authUser->can('ForceDelete:SubscriptionLog');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SubscriptionLog');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SubscriptionLog');
    }

    public function replicate(AuthUser $authUser, SubscriptionLog $subscriptionLog): bool
    {
        return $authUser->can('Replicate:SubscriptionLog');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SubscriptionLog');
    }

}