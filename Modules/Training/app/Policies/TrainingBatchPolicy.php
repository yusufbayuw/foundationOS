<?php

declare(strict_types=1);

namespace Modules\Training\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Training\Models\TrainingBatch;

class TrainingBatchPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:TrainingBatch');
    }

    public function view(AuthUser $authUser, TrainingBatch $trainingBatch): bool
    {
        return $authUser->can('View:TrainingBatch');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:TrainingBatch');
    }

    public function update(AuthUser $authUser, TrainingBatch $trainingBatch): bool
    {
        return $authUser->can('Update:TrainingBatch');
    }

    public function delete(AuthUser $authUser, TrainingBatch $trainingBatch): bool
    {
        return $authUser->can('Delete:TrainingBatch');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:TrainingBatch');
    }

    public function restore(AuthUser $authUser, TrainingBatch $trainingBatch): bool
    {
        return $authUser->can('Restore:TrainingBatch');
    }

    public function forceDelete(AuthUser $authUser, TrainingBatch $trainingBatch): bool
    {
        return $authUser->can('ForceDelete:TrainingBatch');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:TrainingBatch');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:TrainingBatch');
    }

    public function replicate(AuthUser $authUser, TrainingBatch $trainingBatch): bool
    {
        return $authUser->can('Replicate:TrainingBatch');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:TrainingBatch');
    }
}
