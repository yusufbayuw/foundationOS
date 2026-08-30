<?php

declare(strict_types=1);

namespace Modules\Event\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Event\Models\EventCommittee;

class EventCommitteePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:EventCommittee');
    }

    public function view(AuthUser $authUser, EventCommittee $eventCommittee): bool
    {
        return $authUser->can('View:EventCommittee');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:EventCommittee');
    }

    public function update(AuthUser $authUser, EventCommittee $eventCommittee): bool
    {
        return $authUser->can('Update:EventCommittee');
    }

    public function delete(AuthUser $authUser, EventCommittee $eventCommittee): bool
    {
        return $authUser->can('Delete:EventCommittee');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:EventCommittee');
    }

    public function restore(AuthUser $authUser, EventCommittee $eventCommittee): bool
    {
        return $authUser->can('Restore:EventCommittee');
    }

    public function forceDelete(AuthUser $authUser, EventCommittee $eventCommittee): bool
    {
        return $authUser->can('ForceDelete:EventCommittee');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:EventCommittee');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:EventCommittee');
    }

    public function replicate(AuthUser $authUser, EventCommittee $eventCommittee): bool
    {
        return $authUser->can('Replicate:EventCommittee');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:EventCommittee');
    }
}
