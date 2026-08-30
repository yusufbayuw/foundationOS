<?php

declare(strict_types=1);

namespace Modules\ItOps\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\ItOps\Models\BackupJob;

class BackupJobPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:BackupJob');
    }

    public function view(AuthUser $authUser, BackupJob $backupJob): bool
    {
        return $authUser->can('View:BackupJob');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:BackupJob');
    }

    public function update(AuthUser $authUser, BackupJob $backupJob): bool
    {
        return $authUser->can('Update:BackupJob');
    }

    public function delete(AuthUser $authUser, BackupJob $backupJob): bool
    {
        return $authUser->can('Delete:BackupJob');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:BackupJob');
    }

    public function restore(AuthUser $authUser, BackupJob $backupJob): bool
    {
        return $authUser->can('Restore:BackupJob');
    }

    public function forceDelete(AuthUser $authUser, BackupJob $backupJob): bool
    {
        return $authUser->can('ForceDelete:BackupJob');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:BackupJob');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:BackupJob');
    }

    public function replicate(AuthUser $authUser, BackupJob $backupJob): bool
    {
        return $authUser->can('Replicate:BackupJob');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:BackupJob');
    }
}
