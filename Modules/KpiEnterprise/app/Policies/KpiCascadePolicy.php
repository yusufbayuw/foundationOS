<?php

declare(strict_types=1);

namespace Modules\KpiEnterprise\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\KpiEnterprise\Models\KpiCascade;

class KpiCascadePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:KpiCascade');
    }

    public function view(AuthUser $authUser, KpiCascade $kpiCascade): bool
    {
        return $authUser->can('View:KpiCascade');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:KpiCascade');
    }

    public function update(AuthUser $authUser, KpiCascade $kpiCascade): bool
    {
        return $authUser->can('Update:KpiCascade');
    }

    public function delete(AuthUser $authUser, KpiCascade $kpiCascade): bool
    {
        return $authUser->can('Delete:KpiCascade');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:KpiCascade');
    }

    public function restore(AuthUser $authUser, KpiCascade $kpiCascade): bool
    {
        return $authUser->can('Restore:KpiCascade');
    }

    public function forceDelete(AuthUser $authUser, KpiCascade $kpiCascade): bool
    {
        return $authUser->can('ForceDelete:KpiCascade');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:KpiCascade');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:KpiCascade');
    }

    public function replicate(AuthUser $authUser, KpiCascade $kpiCascade): bool
    {
        return $authUser->can('Replicate:KpiCascade');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:KpiCascade');
    }
}
