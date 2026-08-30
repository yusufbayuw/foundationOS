<?php

declare(strict_types=1);

namespace Modules\Transport\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Transport\Models\BoardingLog;

class BoardingLogPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:BoardingLog');
    }

    public function view(AuthUser $authUser, BoardingLog $boardingLog): bool
    {
        return $authUser->can('View:BoardingLog');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:BoardingLog');
    }

    public function update(AuthUser $authUser, BoardingLog $boardingLog): bool
    {
        return $authUser->can('Update:BoardingLog');
    }

    public function delete(AuthUser $authUser, BoardingLog $boardingLog): bool
    {
        return $authUser->can('Delete:BoardingLog');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:BoardingLog');
    }

    public function restore(AuthUser $authUser, BoardingLog $boardingLog): bool
    {
        return $authUser->can('Restore:BoardingLog');
    }

    public function forceDelete(AuthUser $authUser, BoardingLog $boardingLog): bool
    {
        return $authUser->can('ForceDelete:BoardingLog');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:BoardingLog');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:BoardingLog');
    }

    public function replicate(AuthUser $authUser, BoardingLog $boardingLog): bool
    {
        return $authUser->can('Replicate:BoardingLog');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:BoardingLog');
    }
}
