<?php

declare(strict_types=1);

namespace Modules\Employee\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Employee\Models\KpiIndicator;
use Illuminate\Auth\Access\HandlesAuthorization;

class KpiIndicatorPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:KpiIndicator');
    }

    public function view(AuthUser $authUser, KpiIndicator $kpiIndicator): bool
    {
        return $authUser->can('View:KpiIndicator');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:KpiIndicator');
    }

    public function update(AuthUser $authUser, KpiIndicator $kpiIndicator): bool
    {
        return $authUser->can('Update:KpiIndicator');
    }

    public function delete(AuthUser $authUser, KpiIndicator $kpiIndicator): bool
    {
        return $authUser->can('Delete:KpiIndicator');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:KpiIndicator');
    }

    public function restore(AuthUser $authUser, KpiIndicator $kpiIndicator): bool
    {
        return $authUser->can('Restore:KpiIndicator');
    }

    public function forceDelete(AuthUser $authUser, KpiIndicator $kpiIndicator): bool
    {
        return $authUser->can('ForceDelete:KpiIndicator');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:KpiIndicator');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:KpiIndicator');
    }

    public function replicate(AuthUser $authUser, KpiIndicator $kpiIndicator): bool
    {
        return $authUser->can('Replicate:KpiIndicator');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:KpiIndicator');
    }

}