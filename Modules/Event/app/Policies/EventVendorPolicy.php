<?php

declare(strict_types=1);

namespace Modules\Event\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Event\Models\EventVendor;

class EventVendorPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:EventVendor');
    }

    public function view(AuthUser $authUser, EventVendor $eventVendor): bool
    {
        return $authUser->can('View:EventVendor');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:EventVendor');
    }

    public function update(AuthUser $authUser, EventVendor $eventVendor): bool
    {
        return $authUser->can('Update:EventVendor');
    }

    public function delete(AuthUser $authUser, EventVendor $eventVendor): bool
    {
        return $authUser->can('Delete:EventVendor');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:EventVendor');
    }

    public function restore(AuthUser $authUser, EventVendor $eventVendor): bool
    {
        return $authUser->can('Restore:EventVendor');
    }

    public function forceDelete(AuthUser $authUser, EventVendor $eventVendor): bool
    {
        return $authUser->can('ForceDelete:EventVendor');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:EventVendor');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:EventVendor');
    }

    public function replicate(AuthUser $authUser, EventVendor $eventVendor): bool
    {
        return $authUser->can('Replicate:EventVendor');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:EventVendor');
    }
}
