<?php

declare(strict_types=1);

namespace Modules\Helpdesk\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Helpdesk\Models\TicketAttachment;

class TicketAttachmentPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:TicketAttachment');
    }

    public function view(AuthUser $authUser, TicketAttachment $ticketAttachment): bool
    {
        return $authUser->can('View:TicketAttachment');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:TicketAttachment');
    }

    public function update(AuthUser $authUser, TicketAttachment $ticketAttachment): bool
    {
        return $authUser->can('Update:TicketAttachment');
    }

    public function delete(AuthUser $authUser, TicketAttachment $ticketAttachment): bool
    {
        return $authUser->can('Delete:TicketAttachment');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:TicketAttachment');
    }

    public function restore(AuthUser $authUser, TicketAttachment $ticketAttachment): bool
    {
        return $authUser->can('Restore:TicketAttachment');
    }

    public function forceDelete(AuthUser $authUser, TicketAttachment $ticketAttachment): bool
    {
        return $authUser->can('ForceDelete:TicketAttachment');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:TicketAttachment');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:TicketAttachment');
    }

    public function replicate(AuthUser $authUser, TicketAttachment $ticketAttachment): bool
    {
        return $authUser->can('Replicate:TicketAttachment');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:TicketAttachment');
    }
}
