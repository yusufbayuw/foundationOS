<?php

declare(strict_types=1);

namespace Modules\Messaging\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Messaging\Models\NotificationDelivery;

class NotificationDeliveryPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:NotificationDelivery');
    }

    public function view(AuthUser $authUser, NotificationDelivery $notificationDelivery): bool
    {
        return $authUser->can('View:NotificationDelivery');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:NotificationDelivery');
    }

    public function update(AuthUser $authUser, NotificationDelivery $notificationDelivery): bool
    {
        return $authUser->can('Update:NotificationDelivery');
    }

    public function delete(AuthUser $authUser, NotificationDelivery $notificationDelivery): bool
    {
        return $authUser->can('Delete:NotificationDelivery');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:NotificationDelivery');
    }

    public function restore(AuthUser $authUser, NotificationDelivery $notificationDelivery): bool
    {
        return $authUser->can('Restore:NotificationDelivery');
    }

    public function forceDelete(AuthUser $authUser, NotificationDelivery $notificationDelivery): bool
    {
        return $authUser->can('ForceDelete:NotificationDelivery');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:NotificationDelivery');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:NotificationDelivery');
    }

    public function replicate(AuthUser $authUser, NotificationDelivery $notificationDelivery): bool
    {
        return $authUser->can('Replicate:NotificationDelivery');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:NotificationDelivery');
    }
}
