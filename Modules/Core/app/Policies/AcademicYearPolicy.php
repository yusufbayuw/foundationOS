<?php

declare(strict_types=1);

namespace Modules\Core\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Core\Models\AcademicYear;
use Modules\Core\Policies\Concerns\AuthorizesTenantScopedRecord;

class AcademicYearPolicy
{
    use AuthorizesTenantScopedRecord;
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AcademicYear');
    }

    public function view(AuthUser $authUser, AcademicYear $academicYear): bool
    {
        return $authUser->can('View:AcademicYear')
            && $this->belongsToActiveTenant($authUser, $academicYear);
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AcademicYear');
    }

    public function update(AuthUser $authUser, AcademicYear $academicYear): bool
    {
        return $authUser->can('Update:AcademicYear')
            && $this->belongsToActiveTenant($authUser, $academicYear);
    }

    public function delete(AuthUser $authUser, AcademicYear $academicYear): bool
    {
        return $authUser->can('Delete:AcademicYear')
            && $this->belongsToActiveTenant($authUser, $academicYear);
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:AcademicYear');
    }

    public function restore(AuthUser $authUser, AcademicYear $academicYear): bool
    {
        return $authUser->can('Restore:AcademicYear')
            && $this->belongsToActiveTenant($authUser, $academicYear);
    }

    public function forceDelete(AuthUser $authUser, AcademicYear $academicYear): bool
    {
        return $authUser->can('ForceDelete:AcademicYear')
            && $this->belongsToActiveTenant($authUser, $academicYear);
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:AcademicYear');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:AcademicYear');
    }

    public function replicate(AuthUser $authUser, AcademicYear $academicYear): bool
    {
        return $authUser->can('Replicate:AcademicYear')
            && $this->belongsToActiveTenant($authUser, $academicYear);
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:AcademicYear');
    }
}
