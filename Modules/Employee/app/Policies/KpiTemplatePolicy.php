<?php

declare(strict_types=1);

namespace Modules\Employee\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Employee\Models\KpiTemplate;

class KpiTemplatePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:KpiTemplate');
    }

    public function view(AuthUser $authUser, KpiTemplate $kpiTemplate): bool
    {
        return $authUser->can('View:KpiTemplate');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:KpiTemplate');
    }

    public function update(AuthUser $authUser, KpiTemplate $kpiTemplate): bool
    {
        return $authUser->can('Update:KpiTemplate');
    }

    public function delete(AuthUser $authUser, KpiTemplate $kpiTemplate): bool
    {
        return $authUser->can('Delete:KpiTemplate');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:KpiTemplate');
    }

    public function restore(AuthUser $authUser, KpiTemplate $kpiTemplate): bool
    {
        return $authUser->can('Restore:KpiTemplate');
    }

    public function forceDelete(AuthUser $authUser, KpiTemplate $kpiTemplate): bool
    {
        return $authUser->can('ForceDelete:KpiTemplate');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:KpiTemplate');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:KpiTemplate');
    }

    public function replicate(AuthUser $authUser, KpiTemplate $kpiTemplate): bool
    {
        return $authUser->can('Replicate:KpiTemplate');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:KpiTemplate');
    }
}
