<?php

declare(strict_types=1);

namespace Modules\EducationQa\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\EducationQa\Models\AccreditationCycle;

class AccreditationCyclePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AccreditationCycle');
    }

    public function view(AuthUser $authUser, AccreditationCycle $accreditationCycle): bool
    {
        return $authUser->can('View:AccreditationCycle');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AccreditationCycle');
    }

    public function update(AuthUser $authUser, AccreditationCycle $accreditationCycle): bool
    {
        return $authUser->can('Update:AccreditationCycle');
    }

    public function delete(AuthUser $authUser, AccreditationCycle $accreditationCycle): bool
    {
        return $authUser->can('Delete:AccreditationCycle');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:AccreditationCycle');
    }

    public function restore(AuthUser $authUser, AccreditationCycle $accreditationCycle): bool
    {
        return $authUser->can('Restore:AccreditationCycle');
    }

    public function forceDelete(AuthUser $authUser, AccreditationCycle $accreditationCycle): bool
    {
        return $authUser->can('ForceDelete:AccreditationCycle');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:AccreditationCycle');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:AccreditationCycle');
    }

    public function replicate(AuthUser $authUser, AccreditationCycle $accreditationCycle): bool
    {
        return $authUser->can('Replicate:AccreditationCycle');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:AccreditationCycle');
    }
}
