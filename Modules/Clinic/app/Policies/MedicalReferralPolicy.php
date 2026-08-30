<?php

declare(strict_types=1);

namespace Modules\Clinic\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Clinic\Models\MedicalReferral;

class MedicalReferralPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:MedicalReferral');
    }

    public function view(AuthUser $authUser, MedicalReferral $medicalReferral): bool
    {
        return $authUser->can('View:MedicalReferral');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:MedicalReferral');
    }

    public function update(AuthUser $authUser, MedicalReferral $medicalReferral): bool
    {
        return $authUser->can('Update:MedicalReferral');
    }

    public function delete(AuthUser $authUser, MedicalReferral $medicalReferral): bool
    {
        return $authUser->can('Delete:MedicalReferral');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:MedicalReferral');
    }

    public function restore(AuthUser $authUser, MedicalReferral $medicalReferral): bool
    {
        return $authUser->can('Restore:MedicalReferral');
    }

    public function forceDelete(AuthUser $authUser, MedicalReferral $medicalReferral): bool
    {
        return $authUser->can('ForceDelete:MedicalReferral');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:MedicalReferral');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:MedicalReferral');
    }

    public function replicate(AuthUser $authUser, MedicalReferral $medicalReferral): bool
    {
        return $authUser->can('Replicate:MedicalReferral');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:MedicalReferral');
    }
}
