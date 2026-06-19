<?php

declare(strict_types=1);

namespace Modules\School\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\School\Models\AchievementType;

class AchievementTypePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AchievementType');
    }

    public function view(AuthUser $authUser, AchievementType $achievementType): bool
    {
        return $authUser->can('View:AchievementType');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AchievementType');
    }

    public function update(AuthUser $authUser, AchievementType $achievementType): bool
    {
        return $authUser->can('Update:AchievementType');
    }

    public function delete(AuthUser $authUser, AchievementType $achievementType): bool
    {
        return $authUser->can('Delete:AchievementType');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:AchievementType');
    }

    public function restore(AuthUser $authUser, AchievementType $achievementType): bool
    {
        return $authUser->can('Restore:AchievementType');
    }

    public function forceDelete(AuthUser $authUser, AchievementType $achievementType): bool
    {
        return $authUser->can('ForceDelete:AchievementType');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:AchievementType');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:AchievementType');
    }

    public function replicate(AuthUser $authUser, AchievementType $achievementType): bool
    {
        return $authUser->can('Replicate:AchievementType');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:AchievementType');
    }
}
