<?php

declare(strict_types=1);

namespace Modules\Boarding\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Boarding\Models\BoardingMealRecord;

class BoardingMealRecordPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:BoardingMealRecord');
    }

    public function view(AuthUser $authUser, BoardingMealRecord $boardingMealRecord): bool
    {
        return $authUser->can('View:BoardingMealRecord');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:BoardingMealRecord');
    }

    public function update(AuthUser $authUser, BoardingMealRecord $boardingMealRecord): bool
    {
        return $authUser->can('Update:BoardingMealRecord');
    }

    public function delete(AuthUser $authUser, BoardingMealRecord $boardingMealRecord): bool
    {
        return $authUser->can('Delete:BoardingMealRecord');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:BoardingMealRecord');
    }

    public function restore(AuthUser $authUser, BoardingMealRecord $boardingMealRecord): bool
    {
        return $authUser->can('Restore:BoardingMealRecord');
    }

    public function forceDelete(AuthUser $authUser, BoardingMealRecord $boardingMealRecord): bool
    {
        return $authUser->can('ForceDelete:BoardingMealRecord');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:BoardingMealRecord');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:BoardingMealRecord');
    }

    public function replicate(AuthUser $authUser, BoardingMealRecord $boardingMealRecord): bool
    {
        return $authUser->can('Replicate:BoardingMealRecord');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:BoardingMealRecord');
    }
}
