<?php

declare(strict_types=1);

namespace Modules\Helpdesk\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Helpdesk\Models\TicketCategory;

class TicketCategoryPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:TicketCategory');
    }

    public function view(AuthUser $authUser, TicketCategory $ticketCategory): bool
    {
        return $authUser->can('View:TicketCategory');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:TicketCategory');
    }

    public function update(AuthUser $authUser, TicketCategory $ticketCategory): bool
    {
        return $authUser->can('Update:TicketCategory');
    }

    public function delete(AuthUser $authUser, TicketCategory $ticketCategory): bool
    {
        return $authUser->can('Delete:TicketCategory');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:TicketCategory');
    }

    public function restore(AuthUser $authUser, TicketCategory $ticketCategory): bool
    {
        return $authUser->can('Restore:TicketCategory');
    }

    public function forceDelete(AuthUser $authUser, TicketCategory $ticketCategory): bool
    {
        return $authUser->can('ForceDelete:TicketCategory');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:TicketCategory');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:TicketCategory');
    }

    public function replicate(AuthUser $authUser, TicketCategory $ticketCategory): bool
    {
        return $authUser->can('Replicate:TicketCategory');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:TicketCategory');
    }
}
