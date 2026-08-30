<?php

declare(strict_types=1);

namespace Modules\Helpdesk\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Helpdesk\Models\TicketSatisfaction;

class TicketSatisfactionPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:TicketSatisfaction');
    }

    public function view(AuthUser $authUser, TicketSatisfaction $ticketSatisfaction): bool
    {
        return $authUser->can('View:TicketSatisfaction');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:TicketSatisfaction');
    }

    public function update(AuthUser $authUser, TicketSatisfaction $ticketSatisfaction): bool
    {
        return $authUser->can('Update:TicketSatisfaction');
    }

    public function delete(AuthUser $authUser, TicketSatisfaction $ticketSatisfaction): bool
    {
        return $authUser->can('Delete:TicketSatisfaction');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:TicketSatisfaction');
    }

    public function restore(AuthUser $authUser, TicketSatisfaction $ticketSatisfaction): bool
    {
        return $authUser->can('Restore:TicketSatisfaction');
    }

    public function forceDelete(AuthUser $authUser, TicketSatisfaction $ticketSatisfaction): bool
    {
        return $authUser->can('ForceDelete:TicketSatisfaction');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:TicketSatisfaction');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:TicketSatisfaction');
    }

    public function replicate(AuthUser $authUser, TicketSatisfaction $ticketSatisfaction): bool
    {
        return $authUser->can('Replicate:TicketSatisfaction');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:TicketSatisfaction');
    }
}
