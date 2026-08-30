<?php

declare(strict_types=1);

namespace Modules\KpiEnterprise\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\KpiEnterprise\Models\KpiWeight;

class KpiWeightPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:KpiWeight');
    }

    public function view(AuthUser $authUser, KpiWeight $kpiWeight): bool
    {
        return $authUser->can('View:KpiWeight');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:KpiWeight');
    }

    public function update(AuthUser $authUser, KpiWeight $kpiWeight): bool
    {
        return $authUser->can('Update:KpiWeight');
    }

    public function delete(AuthUser $authUser, KpiWeight $kpiWeight): bool
    {
        return $authUser->can('Delete:KpiWeight');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:KpiWeight');
    }

    public function restore(AuthUser $authUser, KpiWeight $kpiWeight): bool
    {
        return $authUser->can('Restore:KpiWeight');
    }

    public function forceDelete(AuthUser $authUser, KpiWeight $kpiWeight): bool
    {
        return $authUser->can('ForceDelete:KpiWeight');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:KpiWeight');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:KpiWeight');
    }

    public function replicate(AuthUser $authUser, KpiWeight $kpiWeight): bool
    {
        return $authUser->can('Replicate:KpiWeight');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:KpiWeight');
    }
}
