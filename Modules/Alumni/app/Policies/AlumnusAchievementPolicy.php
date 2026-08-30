<?php

declare(strict_types=1);

namespace Modules\Alumni\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Alumni\Models\AlumnusAchievement;

class AlumnusAchievementPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AlumnusAchievement');
    }

    public function view(AuthUser $authUser, AlumnusAchievement $alumnusAchievement): bool
    {
        return $authUser->can('View:AlumnusAchievement');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AlumnusAchievement');
    }

    public function update(AuthUser $authUser, AlumnusAchievement $alumnusAchievement): bool
    {
        return $authUser->can('Update:AlumnusAchievement');
    }

    public function delete(AuthUser $authUser, AlumnusAchievement $alumnusAchievement): bool
    {
        return $authUser->can('Delete:AlumnusAchievement');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:AlumnusAchievement');
    }

    public function restore(AuthUser $authUser, AlumnusAchievement $alumnusAchievement): bool
    {
        return $authUser->can('Restore:AlumnusAchievement');
    }

    public function forceDelete(AuthUser $authUser, AlumnusAchievement $alumnusAchievement): bool
    {
        return $authUser->can('ForceDelete:AlumnusAchievement');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:AlumnusAchievement');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:AlumnusAchievement');
    }

    public function replicate(AuthUser $authUser, AlumnusAchievement $alumnusAchievement): bool
    {
        return $authUser->can('Replicate:AlumnusAchievement');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:AlumnusAchievement');
    }
}
