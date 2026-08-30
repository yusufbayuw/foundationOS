<?php

declare(strict_types=1);

namespace Modules\Cafeteria\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Cafeteria\Models\MealSubscription;

class MealSubscriptionPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:MealSubscription');
    }

    public function view(AuthUser $authUser, MealSubscription $mealSubscription): bool
    {
        return $authUser->can('View:MealSubscription');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:MealSubscription');
    }

    public function update(AuthUser $authUser, MealSubscription $mealSubscription): bool
    {
        return $authUser->can('Update:MealSubscription');
    }

    public function delete(AuthUser $authUser, MealSubscription $mealSubscription): bool
    {
        return $authUser->can('Delete:MealSubscription');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:MealSubscription');
    }

    public function restore(AuthUser $authUser, MealSubscription $mealSubscription): bool
    {
        return $authUser->can('Restore:MealSubscription');
    }

    public function forceDelete(AuthUser $authUser, MealSubscription $mealSubscription): bool
    {
        return $authUser->can('ForceDelete:MealSubscription');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:MealSubscription');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:MealSubscription');
    }

    public function replicate(AuthUser $authUser, MealSubscription $mealSubscription): bool
    {
        return $authUser->can('Replicate:MealSubscription');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:MealSubscription');
    }
}
