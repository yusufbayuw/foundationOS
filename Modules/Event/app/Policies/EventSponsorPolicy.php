<?php

declare(strict_types=1);

namespace Modules\Event\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Event\Models\EventSponsor;

class EventSponsorPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:EventSponsor');
    }

    public function view(AuthUser $authUser, EventSponsor $eventSponsor): bool
    {
        return $authUser->can('View:EventSponsor');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:EventSponsor');
    }

    public function update(AuthUser $authUser, EventSponsor $eventSponsor): bool
    {
        return $authUser->can('Update:EventSponsor');
    }

    public function delete(AuthUser $authUser, EventSponsor $eventSponsor): bool
    {
        return $authUser->can('Delete:EventSponsor');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:EventSponsor');
    }

    public function restore(AuthUser $authUser, EventSponsor $eventSponsor): bool
    {
        return $authUser->can('Restore:EventSponsor');
    }

    public function forceDelete(AuthUser $authUser, EventSponsor $eventSponsor): bool
    {
        return $authUser->can('ForceDelete:EventSponsor');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:EventSponsor');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:EventSponsor');
    }

    public function replicate(AuthUser $authUser, EventSponsor $eventSponsor): bool
    {
        return $authUser->can('Replicate:EventSponsor');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:EventSponsor');
    }
}
