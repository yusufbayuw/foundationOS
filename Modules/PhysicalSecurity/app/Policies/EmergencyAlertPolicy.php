<?php

declare(strict_types=1);

namespace Modules\PhysicalSecurity\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\PhysicalSecurity\Models\EmergencyAlert;

class EmergencyAlertPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:EmergencyAlert');
    }

    public function view(AuthUser $authUser, EmergencyAlert $emergencyAlert): bool
    {
        return $authUser->can('View:EmergencyAlert');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:EmergencyAlert');
    }

    public function update(AuthUser $authUser, EmergencyAlert $emergencyAlert): bool
    {
        return $authUser->can('Update:EmergencyAlert');
    }

    public function delete(AuthUser $authUser, EmergencyAlert $emergencyAlert): bool
    {
        return $authUser->can('Delete:EmergencyAlert');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:EmergencyAlert');
    }

    public function restore(AuthUser $authUser, EmergencyAlert $emergencyAlert): bool
    {
        return $authUser->can('Restore:EmergencyAlert');
    }

    public function forceDelete(AuthUser $authUser, EmergencyAlert $emergencyAlert): bool
    {
        return $authUser->can('ForceDelete:EmergencyAlert');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:EmergencyAlert');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:EmergencyAlert');
    }

    public function replicate(AuthUser $authUser, EmergencyAlert $emergencyAlert): bool
    {
        return $authUser->can('Replicate:EmergencyAlert');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:EmergencyAlert');
    }
}
