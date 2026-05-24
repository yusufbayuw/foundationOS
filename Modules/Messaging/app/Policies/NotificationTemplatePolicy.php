<?php

declare(strict_types=1);

namespace Modules\Messaging\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Messaging\Models\NotificationTemplate;

class NotificationTemplatePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:NotificationTemplate');
    }

    public function view(AuthUser $authUser, NotificationTemplate $notificationTemplate): bool
    {
        return $authUser->can('View:NotificationTemplate');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:NotificationTemplate');
    }

    public function update(AuthUser $authUser, NotificationTemplate $notificationTemplate): bool
    {
        return $authUser->can('Update:NotificationTemplate');
    }

    public function delete(AuthUser $authUser, NotificationTemplate $notificationTemplate): bool
    {
        return $authUser->can('Delete:NotificationTemplate');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:NotificationTemplate');
    }

    public function restore(AuthUser $authUser, NotificationTemplate $notificationTemplate): bool
    {
        return $authUser->can('Restore:NotificationTemplate');
    }

    public function forceDelete(AuthUser $authUser, NotificationTemplate $notificationTemplate): bool
    {
        return $authUser->can('ForceDelete:NotificationTemplate');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:NotificationTemplate');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:NotificationTemplate');
    }

    public function replicate(AuthUser $authUser, NotificationTemplate $notificationTemplate): bool
    {
        return $authUser->can('Replicate:NotificationTemplate');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:NotificationTemplate');
    }
}
