<?php

declare(strict_types=1);

namespace Modules\Monitoring\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Monitoring\Models\WebhookDelivery;

class WebhookDeliveryPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:WebhookDelivery');
    }

    public function view(AuthUser $authUser, WebhookDelivery $webhookDelivery): bool
    {
        return $authUser->can('View:WebhookDelivery');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:WebhookDelivery');
    }

    public function update(AuthUser $authUser, WebhookDelivery $webhookDelivery): bool
    {
        return $authUser->can('Update:WebhookDelivery');
    }

    public function delete(AuthUser $authUser, WebhookDelivery $webhookDelivery): bool
    {
        return $authUser->can('Delete:WebhookDelivery');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:WebhookDelivery');
    }

    public function restore(AuthUser $authUser, WebhookDelivery $webhookDelivery): bool
    {
        return $authUser->can('Restore:WebhookDelivery');
    }

    public function forceDelete(AuthUser $authUser, WebhookDelivery $webhookDelivery): bool
    {
        return $authUser->can('ForceDelete:WebhookDelivery');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:WebhookDelivery');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:WebhookDelivery');
    }

    public function replicate(AuthUser $authUser, WebhookDelivery $webhookDelivery): bool
    {
        return $authUser->can('Replicate:WebhookDelivery');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:WebhookDelivery');
    }
}
