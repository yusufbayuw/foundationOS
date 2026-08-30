<?php

declare(strict_types=1);

namespace Modules\Transport\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Transport\Models\RouteStop;

class RouteStopPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:RouteStop');
    }

    public function view(AuthUser $authUser, RouteStop $routeStop): bool
    {
        return $authUser->can('View:RouteStop');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:RouteStop');
    }

    public function update(AuthUser $authUser, RouteStop $routeStop): bool
    {
        return $authUser->can('Update:RouteStop');
    }

    public function delete(AuthUser $authUser, RouteStop $routeStop): bool
    {
        return $authUser->can('Delete:RouteStop');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:RouteStop');
    }

    public function restore(AuthUser $authUser, RouteStop $routeStop): bool
    {
        return $authUser->can('Restore:RouteStop');
    }

    public function forceDelete(AuthUser $authUser, RouteStop $routeStop): bool
    {
        return $authUser->can('ForceDelete:RouteStop');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:RouteStop');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:RouteStop');
    }

    public function replicate(AuthUser $authUser, RouteStop $routeStop): bool
    {
        return $authUser->can('Replicate:RouteStop');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:RouteStop');
    }
}
