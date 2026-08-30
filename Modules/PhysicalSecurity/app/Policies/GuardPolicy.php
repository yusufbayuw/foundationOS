<?php

declare(strict_types=1);

namespace Modules\PhysicalSecurity\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\PhysicalSecurity\Models\Guard;

class GuardPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Guard');
    }

    public function view(AuthUser $authUser, Guard $guard): bool
    {
        return $authUser->can('View:Guard');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Guard');
    }

    public function update(AuthUser $authUser, Guard $guard): bool
    {
        return $authUser->can('Update:Guard');
    }

    public function delete(AuthUser $authUser, Guard $guard): bool
    {
        return $authUser->can('Delete:Guard');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Guard');
    }

    public function restore(AuthUser $authUser, Guard $guard): bool
    {
        return $authUser->can('Restore:Guard');
    }

    public function forceDelete(AuthUser $authUser, Guard $guard): bool
    {
        return $authUser->can('ForceDelete:Guard');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Guard');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Guard');
    }

    public function replicate(AuthUser $authUser, Guard $guard): bool
    {
        return $authUser->can('Replicate:Guard');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Guard');
    }
}
