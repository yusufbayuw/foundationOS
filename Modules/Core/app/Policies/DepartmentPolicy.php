<?php

declare(strict_types=1);

namespace Modules\Core\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Core\Models\Department;
use Modules\Core\Policies\Concerns\AuthorizesTenantScopedRecord;

class DepartmentPolicy
{
    use AuthorizesTenantScopedRecord;
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Department');
    }

    public function view(AuthUser $authUser, Department $department): bool
    {
        return $authUser->can('View:Department')
            && $this->belongsToActiveTenant($authUser, $department);
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Department');
    }

    public function update(AuthUser $authUser, Department $department): bool
    {
        return $authUser->can('Update:Department')
            && $this->belongsToActiveTenant($authUser, $department);
    }

    public function delete(AuthUser $authUser, Department $department): bool
    {
        return $authUser->can('Delete:Department')
            && $this->belongsToActiveTenant($authUser, $department);
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Department');
    }

    public function restore(AuthUser $authUser, Department $department): bool
    {
        return $authUser->can('Restore:Department')
            && $this->belongsToActiveTenant($authUser, $department);
    }

    public function forceDelete(AuthUser $authUser, Department $department): bool
    {
        return $authUser->can('ForceDelete:Department')
            && $this->belongsToActiveTenant($authUser, $department);
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Department');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Department');
    }

    public function replicate(AuthUser $authUser, Department $department): bool
    {
        return $authUser->can('Replicate:Department')
            && $this->belongsToActiveTenant($authUser, $department);
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Department');
    }
}
