<?php

declare(strict_types=1);

namespace Modules\Risk\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Risk\Models\RiskIncident;

class RiskIncidentPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:RiskIncident');
    }

    public function view(AuthUser $authUser, RiskIncident $riskIncident): bool
    {
        return $authUser->can('View:RiskIncident');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:RiskIncident');
    }

    public function update(AuthUser $authUser, RiskIncident $riskIncident): bool
    {
        return $authUser->can('Update:RiskIncident');
    }

    public function delete(AuthUser $authUser, RiskIncident $riskIncident): bool
    {
        return $authUser->can('Delete:RiskIncident');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:RiskIncident');
    }

    public function restore(AuthUser $authUser, RiskIncident $riskIncident): bool
    {
        return $authUser->can('Restore:RiskIncident');
    }

    public function forceDelete(AuthUser $authUser, RiskIncident $riskIncident): bool
    {
        return $authUser->can('ForceDelete:RiskIncident');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:RiskIncident');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:RiskIncident');
    }

    public function replicate(AuthUser $authUser, RiskIncident $riskIncident): bool
    {
        return $authUser->can('Replicate:RiskIncident');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:RiskIncident');
    }
}
