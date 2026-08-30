<?php

declare(strict_types=1);

namespace Modules\KpiEnterprise\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\KpiEnterprise\Models\KpiArea;

class KpiAreaPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:KpiArea');
    }

    public function view(AuthUser $authUser, KpiArea $kpiArea): bool
    {
        return $authUser->can('View:KpiArea');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:KpiArea');
    }

    public function update(AuthUser $authUser, KpiArea $kpiArea): bool
    {
        return $authUser->can('Update:KpiArea');
    }

    public function delete(AuthUser $authUser, KpiArea $kpiArea): bool
    {
        return $authUser->can('Delete:KpiArea');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:KpiArea');
    }

    public function restore(AuthUser $authUser, KpiArea $kpiArea): bool
    {
        return $authUser->can('Restore:KpiArea');
    }

    public function forceDelete(AuthUser $authUser, KpiArea $kpiArea): bool
    {
        return $authUser->can('ForceDelete:KpiArea');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:KpiArea');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:KpiArea');
    }

    public function replicate(AuthUser $authUser, KpiArea $kpiArea): bool
    {
        return $authUser->can('Replicate:KpiArea');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:KpiArea');
    }
}
