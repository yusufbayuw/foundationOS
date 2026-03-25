<?php

declare(strict_types=1);

namespace Modules\Enrollment\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Enrollment\Models\AdmissionPeriod;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdmissionPeriodPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AdmissionPeriod');
    }

    public function view(AuthUser $authUser, AdmissionPeriod $admissionPeriod): bool
    {
        return $authUser->can('View:AdmissionPeriod');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AdmissionPeriod');
    }

    public function update(AuthUser $authUser, AdmissionPeriod $admissionPeriod): bool
    {
        return $authUser->can('Update:AdmissionPeriod');
    }

    public function delete(AuthUser $authUser, AdmissionPeriod $admissionPeriod): bool
    {
        return $authUser->can('Delete:AdmissionPeriod');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:AdmissionPeriod');
    }

    public function restore(AuthUser $authUser, AdmissionPeriod $admissionPeriod): bool
    {
        return $authUser->can('Restore:AdmissionPeriod');
    }

    public function forceDelete(AuthUser $authUser, AdmissionPeriod $admissionPeriod): bool
    {
        return $authUser->can('ForceDelete:AdmissionPeriod');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:AdmissionPeriod');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:AdmissionPeriod');
    }

    public function replicate(AuthUser $authUser, AdmissionPeriod $admissionPeriod): bool
    {
        return $authUser->can('Replicate:AdmissionPeriod');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:AdmissionPeriod');
    }

}