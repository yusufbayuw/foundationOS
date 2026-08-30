<?php

declare(strict_types=1);

namespace Modules\School\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\School\Models\ExtracurricularEnrollment;

class ExtracurricularEnrollmentPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ExtracurricularEnrollment');
    }

    public function view(AuthUser $authUser, ExtracurricularEnrollment $extracurricularEnrollment): bool
    {
        return $authUser->can('View:ExtracurricularEnrollment');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ExtracurricularEnrollment');
    }

    public function update(AuthUser $authUser, ExtracurricularEnrollment $extracurricularEnrollment): bool
    {
        return $authUser->can('Update:ExtracurricularEnrollment');
    }

    public function delete(AuthUser $authUser, ExtracurricularEnrollment $extracurricularEnrollment): bool
    {
        return $authUser->can('Delete:ExtracurricularEnrollment');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ExtracurricularEnrollment');
    }

    public function restore(AuthUser $authUser, ExtracurricularEnrollment $extracurricularEnrollment): bool
    {
        return $authUser->can('Restore:ExtracurricularEnrollment');
    }

    public function forceDelete(AuthUser $authUser, ExtracurricularEnrollment $extracurricularEnrollment): bool
    {
        return $authUser->can('ForceDelete:ExtracurricularEnrollment');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ExtracurricularEnrollment');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ExtracurricularEnrollment');
    }

    public function replicate(AuthUser $authUser, ExtracurricularEnrollment $extracurricularEnrollment): bool
    {
        return $authUser->can('Replicate:ExtracurricularEnrollment');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ExtracurricularEnrollment');
    }
}
