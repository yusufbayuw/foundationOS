<?php

declare(strict_types=1);

namespace Modules\Clinic\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Clinic\Models\MedicalHistory;

class MedicalHistoryPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:MedicalHistory');
    }

    public function view(AuthUser $authUser, MedicalHistory $medicalHistory): bool
    {
        return $authUser->can('View:MedicalHistory');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:MedicalHistory');
    }

    public function update(AuthUser $authUser, MedicalHistory $medicalHistory): bool
    {
        return $authUser->can('Update:MedicalHistory');
    }

    public function delete(AuthUser $authUser, MedicalHistory $medicalHistory): bool
    {
        return $authUser->can('Delete:MedicalHistory');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:MedicalHistory');
    }

    public function restore(AuthUser $authUser, MedicalHistory $medicalHistory): bool
    {
        return $authUser->can('Restore:MedicalHistory');
    }

    public function forceDelete(AuthUser $authUser, MedicalHistory $medicalHistory): bool
    {
        return $authUser->can('ForceDelete:MedicalHistory');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:MedicalHistory');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:MedicalHistory');
    }

    public function replicate(AuthUser $authUser, MedicalHistory $medicalHistory): bool
    {
        return $authUser->can('Replicate:MedicalHistory');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:MedicalHistory');
    }
}
