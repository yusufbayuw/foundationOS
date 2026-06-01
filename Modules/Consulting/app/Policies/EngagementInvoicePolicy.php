<?php

declare(strict_types=1);

namespace Modules\Consulting\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Consulting\Models\EngagementInvoice;
use Modules\Core\Policies\Concerns\AuthorizesPrint;

class EngagementInvoicePolicy
{
    use AuthorizesPrint, HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:EngagementInvoice');
    }

    public function view(AuthUser $authUser, EngagementInvoice $engagementInvoice): bool
    {
        return $authUser->can('View:EngagementInvoice');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:EngagementInvoice');
    }

    public function update(AuthUser $authUser, EngagementInvoice $engagementInvoice): bool
    {
        return $authUser->can('Update:EngagementInvoice');
    }

    public function delete(AuthUser $authUser, EngagementInvoice $engagementInvoice): bool
    {
        return $authUser->can('Delete:EngagementInvoice');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:EngagementInvoice');
    }

    public function restore(AuthUser $authUser, EngagementInvoice $engagementInvoice): bool
    {
        return $authUser->can('Restore:EngagementInvoice');
    }

    public function forceDelete(AuthUser $authUser, EngagementInvoice $engagementInvoice): bool
    {
        return $authUser->can('ForceDelete:EngagementInvoice');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:EngagementInvoice');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:EngagementInvoice');
    }

    public function replicate(AuthUser $authUser, EngagementInvoice $engagementInvoice): bool
    {
        return $authUser->can('Replicate:EngagementInvoice');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:EngagementInvoice');
    }
}
