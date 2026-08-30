<?php

declare(strict_types=1);

namespace Modules\Cafeteria\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Cafeteria\Models\MealRating;

class MealRatingPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:MealRating');
    }

    public function view(AuthUser $authUser, MealRating $mealRating): bool
    {
        return $authUser->can('View:MealRating');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:MealRating');
    }

    public function update(AuthUser $authUser, MealRating $mealRating): bool
    {
        return $authUser->can('Update:MealRating');
    }

    public function delete(AuthUser $authUser, MealRating $mealRating): bool
    {
        return $authUser->can('Delete:MealRating');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:MealRating');
    }

    public function restore(AuthUser $authUser, MealRating $mealRating): bool
    {
        return $authUser->can('Restore:MealRating');
    }

    public function forceDelete(AuthUser $authUser, MealRating $mealRating): bool
    {
        return $authUser->can('ForceDelete:MealRating');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:MealRating');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:MealRating');
    }

    public function replicate(AuthUser $authUser, MealRating $mealRating): bool
    {
        return $authUser->can('Replicate:MealRating');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:MealRating');
    }
}
