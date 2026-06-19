<?php

declare(strict_types=1);

namespace Modules\Core\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Core\Models\AcademicPeriod;

class AcademicPeriodPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AcademicPeriod');
    }

    public function view(AuthUser $authUser, AcademicPeriod $academicPeriod): bool
    {
        return $authUser->can('View:AcademicPeriod');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AcademicPeriod');
    }

    public function update(AuthUser $authUser, AcademicPeriod $academicPeriod): bool
    {
        return $authUser->can('Update:AcademicPeriod');
    }

    public function delete(AuthUser $authUser, AcademicPeriod $academicPeriod): bool
    {
        return $authUser->can('Delete:AcademicPeriod');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:AcademicPeriod');
    }

    public function restore(AuthUser $authUser, AcademicPeriod $academicPeriod): bool
    {
        return $authUser->can('Restore:AcademicPeriod');
    }

    public function forceDelete(AuthUser $authUser, AcademicPeriod $academicPeriod): bool
    {
        return $authUser->can('ForceDelete:AcademicPeriod');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:AcademicPeriod');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:AcademicPeriod');
    }

    public function replicate(AuthUser $authUser, AcademicPeriod $academicPeriod): bool
    {
        return $authUser->can('Replicate:AcademicPeriod');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:AcademicPeriod');
    }
}
