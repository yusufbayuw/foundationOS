<?php

declare(strict_types=1);

namespace Modules\School\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Core\Policies\Concerns\AuthorizesPrint;
use Modules\School\Models\StudentAchievement;

class StudentAchievementPolicy
{
    use AuthorizesPrint, HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:StudentAchievement');
    }

    public function view(AuthUser $authUser, StudentAchievement $studentAchievement): bool
    {
        return $authUser->can('View:StudentAchievement');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:StudentAchievement');
    }

    public function update(AuthUser $authUser, StudentAchievement $studentAchievement): bool
    {
        return $authUser->can('Update:StudentAchievement');
    }

    public function delete(AuthUser $authUser, StudentAchievement $studentAchievement): bool
    {
        return $authUser->can('Delete:StudentAchievement');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:StudentAchievement');
    }

    public function restore(AuthUser $authUser, StudentAchievement $studentAchievement): bool
    {
        return $authUser->can('Restore:StudentAchievement');
    }

    public function forceDelete(AuthUser $authUser, StudentAchievement $studentAchievement): bool
    {
        return $authUser->can('ForceDelete:StudentAchievement');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:StudentAchievement');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:StudentAchievement');
    }

    public function replicate(AuthUser $authUser, StudentAchievement $studentAchievement): bool
    {
        return $authUser->can('Replicate:StudentAchievement');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:StudentAchievement');
    }
}
