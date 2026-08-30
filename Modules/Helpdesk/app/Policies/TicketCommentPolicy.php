<?php

declare(strict_types=1);

namespace Modules\Helpdesk\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Helpdesk\Models\TicketComment;

class TicketCommentPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:TicketComment');
    }

    public function view(AuthUser $authUser, TicketComment $ticketComment): bool
    {
        return $authUser->can('View:TicketComment');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:TicketComment');
    }

    public function update(AuthUser $authUser, TicketComment $ticketComment): bool
    {
        return $authUser->can('Update:TicketComment');
    }

    public function delete(AuthUser $authUser, TicketComment $ticketComment): bool
    {
        return $authUser->can('Delete:TicketComment');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:TicketComment');
    }

    public function restore(AuthUser $authUser, TicketComment $ticketComment): bool
    {
        return $authUser->can('Restore:TicketComment');
    }

    public function forceDelete(AuthUser $authUser, TicketComment $ticketComment): bool
    {
        return $authUser->can('ForceDelete:TicketComment');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:TicketComment');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:TicketComment');
    }

    public function replicate(AuthUser $authUser, TicketComment $ticketComment): bool
    {
        return $authUser->can('Replicate:TicketComment');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:TicketComment');
    }
}
