<?php

declare(strict_types=1);

namespace Modules\Clinic\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Clinic\Models\MedicalConsent;

class MedicalConsentPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:MedicalConsent');
    }

    public function view(AuthUser $authUser, MedicalConsent $medicalConsent): bool
    {
        return $authUser->can('View:MedicalConsent');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:MedicalConsent');
    }

    public function update(AuthUser $authUser, MedicalConsent $medicalConsent): bool
    {
        return $authUser->can('Update:MedicalConsent');
    }

    public function delete(AuthUser $authUser, MedicalConsent $medicalConsent): bool
    {
        return $authUser->can('Delete:MedicalConsent');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:MedicalConsent');
    }

    public function restore(AuthUser $authUser, MedicalConsent $medicalConsent): bool
    {
        return $authUser->can('Restore:MedicalConsent');
    }

    public function forceDelete(AuthUser $authUser, MedicalConsent $medicalConsent): bool
    {
        return $authUser->can('ForceDelete:MedicalConsent');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:MedicalConsent');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:MedicalConsent');
    }

    public function replicate(AuthUser $authUser, MedicalConsent $medicalConsent): bool
    {
        return $authUser->can('Replicate:MedicalConsent');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:MedicalConsent');
    }
}
