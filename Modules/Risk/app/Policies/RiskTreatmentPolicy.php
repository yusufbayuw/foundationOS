<?php

declare(strict_types=1);

namespace Modules\Risk\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Risk\Models\RiskTreatment;

class RiskTreatmentPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:RiskTreatment');
    }

    public function view(AuthUser $authUser, RiskTreatment $riskTreatment): bool
    {
        return $authUser->can('View:RiskTreatment');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:RiskTreatment');
    }

    public function update(AuthUser $authUser, RiskTreatment $riskTreatment): bool
    {
        return $authUser->can('Update:RiskTreatment');
    }

    public function delete(AuthUser $authUser, RiskTreatment $riskTreatment): bool
    {
        return $authUser->can('Delete:RiskTreatment');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:RiskTreatment');
    }

    public function restore(AuthUser $authUser, RiskTreatment $riskTreatment): bool
    {
        return $authUser->can('Restore:RiskTreatment');
    }

    public function forceDelete(AuthUser $authUser, RiskTreatment $riskTreatment): bool
    {
        return $authUser->can('ForceDelete:RiskTreatment');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:RiskTreatment');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:RiskTreatment');
    }

    public function replicate(AuthUser $authUser, RiskTreatment $riskTreatment): bool
    {
        return $authUser->can('Replicate:RiskTreatment');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:RiskTreatment');
    }
}
