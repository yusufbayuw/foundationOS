<?php

declare(strict_types=1);

namespace Modules\Monitoring\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Monitoring\Models\WebhookSubscription;

class WebhookSubscriptionPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:WebhookSubscription');
    }

    public function view(AuthUser $authUser, WebhookSubscription $webhookSubscription): bool
    {
        return $authUser->can('View:WebhookSubscription');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:WebhookSubscription');
    }

    public function update(AuthUser $authUser, WebhookSubscription $webhookSubscription): bool
    {
        return $authUser->can('Update:WebhookSubscription');
    }

    public function delete(AuthUser $authUser, WebhookSubscription $webhookSubscription): bool
    {
        return $authUser->can('Delete:WebhookSubscription');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:WebhookSubscription');
    }

    public function restore(AuthUser $authUser, WebhookSubscription $webhookSubscription): bool
    {
        return $authUser->can('Restore:WebhookSubscription');
    }

    public function forceDelete(AuthUser $authUser, WebhookSubscription $webhookSubscription): bool
    {
        return $authUser->can('ForceDelete:WebhookSubscription');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:WebhookSubscription');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:WebhookSubscription');
    }

    public function replicate(AuthUser $authUser, WebhookSubscription $webhookSubscription): bool
    {
        return $authUser->can('Replicate:WebhookSubscription');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:WebhookSubscription');
    }
}
