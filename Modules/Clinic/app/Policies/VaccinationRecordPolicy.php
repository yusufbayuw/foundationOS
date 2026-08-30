<?php

declare(strict_types=1);

namespace Modules\Clinic\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Clinic\Models\VaccinationRecord;

class VaccinationRecordPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:VaccinationRecord');
    }

    public function view(AuthUser $authUser, VaccinationRecord $vaccinationRecord): bool
    {
        return $authUser->can('View:VaccinationRecord');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:VaccinationRecord');
    }

    public function update(AuthUser $authUser, VaccinationRecord $vaccinationRecord): bool
    {
        return $authUser->can('Update:VaccinationRecord');
    }

    public function delete(AuthUser $authUser, VaccinationRecord $vaccinationRecord): bool
    {
        return $authUser->can('Delete:VaccinationRecord');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:VaccinationRecord');
    }

    public function restore(AuthUser $authUser, VaccinationRecord $vaccinationRecord): bool
    {
        return $authUser->can('Restore:VaccinationRecord');
    }

    public function forceDelete(AuthUser $authUser, VaccinationRecord $vaccinationRecord): bool
    {
        return $authUser->can('ForceDelete:VaccinationRecord');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:VaccinationRecord');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:VaccinationRecord');
    }

    public function replicate(AuthUser $authUser, VaccinationRecord $vaccinationRecord): bool
    {
        return $authUser->can('Replicate:VaccinationRecord');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:VaccinationRecord');
    }
}
