<?php

declare(strict_types=1);

namespace Modules\KpiEnterprise\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\KpiEnterprise\Models\KpiTarget;

class KpiTargetPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:KpiTarget');
    }

    public function view(AuthUser $authUser, KpiTarget $kpiTarget): bool
    {
        return $authUser->can('View:KpiTarget');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:KpiTarget');
    }

    public function update(AuthUser $authUser, KpiTarget $kpiTarget): bool
    {
        return $authUser->can('Update:KpiTarget');
    }

    public function delete(AuthUser $authUser, KpiTarget $kpiTarget): bool
    {
        return $authUser->can('Delete:KpiTarget');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:KpiTarget');
    }

    public function restore(AuthUser $authUser, KpiTarget $kpiTarget): bool
    {
        return $authUser->can('Restore:KpiTarget');
    }

    public function forceDelete(AuthUser $authUser, KpiTarget $kpiTarget): bool
    {
        return $authUser->can('ForceDelete:KpiTarget');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:KpiTarget');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:KpiTarget');
    }

    public function replicate(AuthUser $authUser, KpiTarget $kpiTarget): bool
    {
        return $authUser->can('Replicate:KpiTarget');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:KpiTarget');
    }
}
