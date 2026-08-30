<?php

declare(strict_types=1);

namespace Modules\Training\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Training\Models\TrainingPayment;

class TrainingPaymentPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:TrainingPayment');
    }

    public function view(AuthUser $authUser, TrainingPayment $trainingPayment): bool
    {
        return $authUser->can('View:TrainingPayment');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:TrainingPayment');
    }

    public function update(AuthUser $authUser, TrainingPayment $trainingPayment): bool
    {
        return $authUser->can('Update:TrainingPayment');
    }

    public function delete(AuthUser $authUser, TrainingPayment $trainingPayment): bool
    {
        return $authUser->can('Delete:TrainingPayment');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:TrainingPayment');
    }

    public function restore(AuthUser $authUser, TrainingPayment $trainingPayment): bool
    {
        return $authUser->can('Restore:TrainingPayment');
    }

    public function forceDelete(AuthUser $authUser, TrainingPayment $trainingPayment): bool
    {
        return $authUser->can('ForceDelete:TrainingPayment');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:TrainingPayment');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:TrainingPayment');
    }

    public function replicate(AuthUser $authUser, TrainingPayment $trainingPayment): bool
    {
        return $authUser->can('Replicate:TrainingPayment');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:TrainingPayment');
    }
}
