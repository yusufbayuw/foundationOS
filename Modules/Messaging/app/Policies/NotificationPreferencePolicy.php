<?php

declare(strict_types=1);

namespace Modules\Messaging\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Messaging\Models\NotificationPreference;

class NotificationPreferencePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:NotificationPreference');
    }

    public function view(AuthUser $authUser, NotificationPreference $notificationPreference): bool
    {
        return $authUser->can('View:NotificationPreference');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:NotificationPreference');
    }

    public function update(AuthUser $authUser, NotificationPreference $notificationPreference): bool
    {
        return $authUser->can('Update:NotificationPreference');
    }

    public function delete(AuthUser $authUser, NotificationPreference $notificationPreference): bool
    {
        return $authUser->can('Delete:NotificationPreference');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:NotificationPreference');
    }

    public function restore(AuthUser $authUser, NotificationPreference $notificationPreference): bool
    {
        return $authUser->can('Restore:NotificationPreference');
    }

    public function forceDelete(AuthUser $authUser, NotificationPreference $notificationPreference): bool
    {
        return $authUser->can('ForceDelete:NotificationPreference');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:NotificationPreference');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:NotificationPreference');
    }

    public function replicate(AuthUser $authUser, NotificationPreference $notificationPreference): bool
    {
        return $authUser->can('Replicate:NotificationPreference');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:NotificationPreference');
    }
}
