<?php

declare(strict_types=1);

namespace Modules\Event\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Event\Models\EventParticipant;

class EventParticipantPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:EventParticipant');
    }

    public function view(AuthUser $authUser, EventParticipant $eventParticipant): bool
    {
        return $authUser->can('View:EventParticipant');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:EventParticipant');
    }

    public function update(AuthUser $authUser, EventParticipant $eventParticipant): bool
    {
        return $authUser->can('Update:EventParticipant');
    }

    public function delete(AuthUser $authUser, EventParticipant $eventParticipant): bool
    {
        return $authUser->can('Delete:EventParticipant');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:EventParticipant');
    }

    public function restore(AuthUser $authUser, EventParticipant $eventParticipant): bool
    {
        return $authUser->can('Restore:EventParticipant');
    }

    public function forceDelete(AuthUser $authUser, EventParticipant $eventParticipant): bool
    {
        return $authUser->can('ForceDelete:EventParticipant');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:EventParticipant');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:EventParticipant');
    }

    public function replicate(AuthUser $authUser, EventParticipant $eventParticipant): bool
    {
        return $authUser->can('Replicate:EventParticipant');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:EventParticipant');
    }
}
