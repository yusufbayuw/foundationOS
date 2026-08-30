<?php

declare(strict_types=1);

namespace Modules\Event\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Event\Models\EventCheckIn;

class EventCheckInPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:EventCheckIn');
    }

    public function view(AuthUser $authUser, EventCheckIn $eventCheckIn): bool
    {
        return $authUser->can('View:EventCheckIn');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:EventCheckIn');
    }

    public function update(AuthUser $authUser, EventCheckIn $eventCheckIn): bool
    {
        return $authUser->can('Update:EventCheckIn');
    }

    public function delete(AuthUser $authUser, EventCheckIn $eventCheckIn): bool
    {
        return $authUser->can('Delete:EventCheckIn');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:EventCheckIn');
    }

    public function restore(AuthUser $authUser, EventCheckIn $eventCheckIn): bool
    {
        return $authUser->can('Restore:EventCheckIn');
    }

    public function forceDelete(AuthUser $authUser, EventCheckIn $eventCheckIn): bool
    {
        return $authUser->can('ForceDelete:EventCheckIn');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:EventCheckIn');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:EventCheckIn');
    }

    public function replicate(AuthUser $authUser, EventCheckIn $eventCheckIn): bool
    {
        return $authUser->can('Replicate:EventCheckIn');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:EventCheckIn');
    }
}
