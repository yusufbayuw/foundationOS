<?php

declare(strict_types=1);

namespace Modules\Boarding\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Boarding\Models\LaundryRecord;

class LaundryRecordPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:LaundryRecord');
    }

    public function view(AuthUser $authUser, LaundryRecord $laundryRecord): bool
    {
        return $authUser->can('View:LaundryRecord');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:LaundryRecord');
    }

    public function update(AuthUser $authUser, LaundryRecord $laundryRecord): bool
    {
        return $authUser->can('Update:LaundryRecord');
    }

    public function delete(AuthUser $authUser, LaundryRecord $laundryRecord): bool
    {
        return $authUser->can('Delete:LaundryRecord');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:LaundryRecord');
    }

    public function restore(AuthUser $authUser, LaundryRecord $laundryRecord): bool
    {
        return $authUser->can('Restore:LaundryRecord');
    }

    public function forceDelete(AuthUser $authUser, LaundryRecord $laundryRecord): bool
    {
        return $authUser->can('ForceDelete:LaundryRecord');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:LaundryRecord');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:LaundryRecord');
    }

    public function replicate(AuthUser $authUser, LaundryRecord $laundryRecord): bool
    {
        return $authUser->can('Replicate:LaundryRecord');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:LaundryRecord');
    }
}
