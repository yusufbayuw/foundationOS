<?php

declare(strict_types=1);

namespace Modules\PhysicalSecurity\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\PhysicalSecurity\Models\SecurityIncident;

class SecurityIncidentPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SecurityIncident');
    }

    public function view(AuthUser $authUser, SecurityIncident $securityIncident): bool
    {
        return $authUser->can('View:SecurityIncident');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SecurityIncident');
    }

    public function update(AuthUser $authUser, SecurityIncident $securityIncident): bool
    {
        return $authUser->can('Update:SecurityIncident');
    }

    public function delete(AuthUser $authUser, SecurityIncident $securityIncident): bool
    {
        return $authUser->can('Delete:SecurityIncident');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:SecurityIncident');
    }

    public function restore(AuthUser $authUser, SecurityIncident $securityIncident): bool
    {
        return $authUser->can('Restore:SecurityIncident');
    }

    public function forceDelete(AuthUser $authUser, SecurityIncident $securityIncident): bool
    {
        return $authUser->can('ForceDelete:SecurityIncident');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SecurityIncident');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SecurityIncident');
    }

    public function replicate(AuthUser $authUser, SecurityIncident $securityIncident): bool
    {
        return $authUser->can('Replicate:SecurityIncident');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SecurityIncident');
    }
}
