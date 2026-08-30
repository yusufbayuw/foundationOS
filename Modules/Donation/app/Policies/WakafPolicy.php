<?php

declare(strict_types=1);

namespace Modules\Donation\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Donation\Models\Wakaf;

class WakafPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Wakaf');
    }

    public function view(AuthUser $authUser, Wakaf $wakaf): bool
    {
        return $authUser->can('View:Wakaf');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Wakaf');
    }

    public function update(AuthUser $authUser, Wakaf $wakaf): bool
    {
        return $authUser->can('Update:Wakaf');
    }

    public function delete(AuthUser $authUser, Wakaf $wakaf): bool
    {
        return $authUser->can('Delete:Wakaf');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Wakaf');
    }

    public function restore(AuthUser $authUser, Wakaf $wakaf): bool
    {
        return $authUser->can('Restore:Wakaf');
    }

    public function forceDelete(AuthUser $authUser, Wakaf $wakaf): bool
    {
        return $authUser->can('ForceDelete:Wakaf');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Wakaf');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Wakaf');
    }

    public function replicate(AuthUser $authUser, Wakaf $wakaf): bool
    {
        return $authUser->can('Replicate:Wakaf');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Wakaf');
    }
}
