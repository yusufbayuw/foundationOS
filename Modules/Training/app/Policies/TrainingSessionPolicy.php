<?php

declare(strict_types=1);

namespace Modules\Training\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Training\Models\TrainingSession;

class TrainingSessionPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:TrainingSession');
    }

    public function view(AuthUser $authUser, TrainingSession $trainingSession): bool
    {
        return $authUser->can('View:TrainingSession');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:TrainingSession');
    }

    public function update(AuthUser $authUser, TrainingSession $trainingSession): bool
    {
        return $authUser->can('Update:TrainingSession');
    }

    public function delete(AuthUser $authUser, TrainingSession $trainingSession): bool
    {
        return $authUser->can('Delete:TrainingSession');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:TrainingSession');
    }

    public function restore(AuthUser $authUser, TrainingSession $trainingSession): bool
    {
        return $authUser->can('Restore:TrainingSession');
    }

    public function forceDelete(AuthUser $authUser, TrainingSession $trainingSession): bool
    {
        return $authUser->can('ForceDelete:TrainingSession');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:TrainingSession');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:TrainingSession');
    }

    public function replicate(AuthUser $authUser, TrainingSession $trainingSession): bool
    {
        return $authUser->can('Replicate:TrainingSession');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:TrainingSession');
    }
}
