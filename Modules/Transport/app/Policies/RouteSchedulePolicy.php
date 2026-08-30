<?php

declare(strict_types=1);

namespace Modules\Transport\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Transport\Models\RouteSchedule;

class RouteSchedulePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:RouteSchedule');
    }

    public function view(AuthUser $authUser, RouteSchedule $routeSchedule): bool
    {
        return $authUser->can('View:RouteSchedule');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:RouteSchedule');
    }

    public function update(AuthUser $authUser, RouteSchedule $routeSchedule): bool
    {
        return $authUser->can('Update:RouteSchedule');
    }

    public function delete(AuthUser $authUser, RouteSchedule $routeSchedule): bool
    {
        return $authUser->can('Delete:RouteSchedule');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:RouteSchedule');
    }

    public function restore(AuthUser $authUser, RouteSchedule $routeSchedule): bool
    {
        return $authUser->can('Restore:RouteSchedule');
    }

    public function forceDelete(AuthUser $authUser, RouteSchedule $routeSchedule): bool
    {
        return $authUser->can('ForceDelete:RouteSchedule');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:RouteSchedule');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:RouteSchedule');
    }

    public function replicate(AuthUser $authUser, RouteSchedule $routeSchedule): bool
    {
        return $authUser->can('Replicate:RouteSchedule');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:RouteSchedule');
    }
}
