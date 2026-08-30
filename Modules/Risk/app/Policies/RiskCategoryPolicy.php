<?php

declare(strict_types=1);

namespace Modules\Risk\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Risk\Models\RiskCategory;

class RiskCategoryPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:RiskCategory');
    }

    public function view(AuthUser $authUser, RiskCategory $riskCategory): bool
    {
        return $authUser->can('View:RiskCategory');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:RiskCategory');
    }

    public function update(AuthUser $authUser, RiskCategory $riskCategory): bool
    {
        return $authUser->can('Update:RiskCategory');
    }

    public function delete(AuthUser $authUser, RiskCategory $riskCategory): bool
    {
        return $authUser->can('Delete:RiskCategory');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:RiskCategory');
    }

    public function restore(AuthUser $authUser, RiskCategory $riskCategory): bool
    {
        return $authUser->can('Restore:RiskCategory');
    }

    public function forceDelete(AuthUser $authUser, RiskCategory $riskCategory): bool
    {
        return $authUser->can('ForceDelete:RiskCategory');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:RiskCategory');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:RiskCategory');
    }

    public function replicate(AuthUser $authUser, RiskCategory $riskCategory): bool
    {
        return $authUser->can('Replicate:RiskCategory');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:RiskCategory');
    }
}
