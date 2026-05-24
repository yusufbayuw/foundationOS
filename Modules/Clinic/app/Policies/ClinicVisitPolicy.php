<?php

declare(strict_types=1);

namespace Modules\Clinic\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Clinic\Models\ClinicVisit;

class ClinicVisitPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ClinicVisit');
    }

    public function view(AuthUser $authUser, ClinicVisit $clinicVisit): bool
    {
        return $authUser->can('View:ClinicVisit');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ClinicVisit');
    }

    public function update(AuthUser $authUser, ClinicVisit $clinicVisit): bool
    {
        return $authUser->can('Update:ClinicVisit');
    }

    public function delete(AuthUser $authUser, ClinicVisit $clinicVisit): bool
    {
        return $authUser->can('Delete:ClinicVisit');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ClinicVisit');
    }

    public function restore(AuthUser $authUser, ClinicVisit $clinicVisit): bool
    {
        return $authUser->can('Restore:ClinicVisit');
    }

    public function forceDelete(AuthUser $authUser, ClinicVisit $clinicVisit): bool
    {
        return $authUser->can('ForceDelete:ClinicVisit');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ClinicVisit');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ClinicVisit');
    }

    public function replicate(AuthUser $authUser, ClinicVisit $clinicVisit): bool
    {
        return $authUser->can('Replicate:ClinicVisit');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ClinicVisit');
    }
}
