<?php

declare(strict_types=1);

namespace Modules\Training\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Training\Models\TrainingAffiliateCommission;

class TrainingAffiliateCommissionPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:TrainingAffiliateCommission');
    }

    public function view(AuthUser $authUser, TrainingAffiliateCommission $trainingAffiliateCommission): bool
    {
        return $authUser->can('View:TrainingAffiliateCommission');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:TrainingAffiliateCommission');
    }

    public function update(AuthUser $authUser, TrainingAffiliateCommission $trainingAffiliateCommission): bool
    {
        return $authUser->can('Update:TrainingAffiliateCommission');
    }

    public function delete(AuthUser $authUser, TrainingAffiliateCommission $trainingAffiliateCommission): bool
    {
        return $authUser->can('Delete:TrainingAffiliateCommission');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:TrainingAffiliateCommission');
    }

    public function restore(AuthUser $authUser, TrainingAffiliateCommission $trainingAffiliateCommission): bool
    {
        return $authUser->can('Restore:TrainingAffiliateCommission');
    }

    public function forceDelete(AuthUser $authUser, TrainingAffiliateCommission $trainingAffiliateCommission): bool
    {
        return $authUser->can('ForceDelete:TrainingAffiliateCommission');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:TrainingAffiliateCommission');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:TrainingAffiliateCommission');
    }

    public function replicate(AuthUser $authUser, TrainingAffiliateCommission $trainingAffiliateCommission): bool
    {
        return $authUser->can('Replicate:TrainingAffiliateCommission');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:TrainingAffiliateCommission');
    }
}
