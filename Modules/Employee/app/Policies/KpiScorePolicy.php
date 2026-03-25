<?php

declare(strict_types=1);

namespace Modules\Employee\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Employee\Models\KpiScore;
use Illuminate\Auth\Access\HandlesAuthorization;

class KpiScorePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:KpiScore');
    }

    public function view(AuthUser $authUser, KpiScore $kpiScore): bool
    {
        return $authUser->can('View:KpiScore');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:KpiScore');
    }

    public function update(AuthUser $authUser, KpiScore $kpiScore): bool
    {
        return $authUser->can('Update:KpiScore');
    }

    public function delete(AuthUser $authUser, KpiScore $kpiScore): bool
    {
        return $authUser->can('Delete:KpiScore');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:KpiScore');
    }

    public function restore(AuthUser $authUser, KpiScore $kpiScore): bool
    {
        return $authUser->can('Restore:KpiScore');
    }

    public function forceDelete(AuthUser $authUser, KpiScore $kpiScore): bool
    {
        return $authUser->can('ForceDelete:KpiScore');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:KpiScore');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:KpiScore');
    }

    public function replicate(AuthUser $authUser, KpiScore $kpiScore): bool
    {
        return $authUser->can('Replicate:KpiScore');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:KpiScore');
    }

}