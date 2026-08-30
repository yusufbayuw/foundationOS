<?php

declare(strict_types=1);

namespace Modules\KpiEnterprise\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\KpiEnterprise\Models\KpiActual;

class KpiActualPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:KpiActual');
    }

    public function view(AuthUser $authUser, KpiActual $kpiActual): bool
    {
        return $authUser->can('View:KpiActual');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:KpiActual');
    }

    public function update(AuthUser $authUser, KpiActual $kpiActual): bool
    {
        return $authUser->can('Update:KpiActual');
    }

    public function delete(AuthUser $authUser, KpiActual $kpiActual): bool
    {
        return $authUser->can('Delete:KpiActual');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:KpiActual');
    }

    public function restore(AuthUser $authUser, KpiActual $kpiActual): bool
    {
        return $authUser->can('Restore:KpiActual');
    }

    public function forceDelete(AuthUser $authUser, KpiActual $kpiActual): bool
    {
        return $authUser->can('ForceDelete:KpiActual');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:KpiActual');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:KpiActual');
    }

    public function replicate(AuthUser $authUser, KpiActual $kpiActual): bool
    {
        return $authUser->can('Replicate:KpiActual');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:KpiActual');
    }
}
