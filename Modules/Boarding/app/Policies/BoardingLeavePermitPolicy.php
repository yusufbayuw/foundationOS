<?php

declare(strict_types=1);

namespace Modules\Boarding\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Boarding\Models\BoardingLeavePermit;
use Modules\Core\Policies\Concerns\AuthorizesPrint;

class BoardingLeavePermitPolicy
{
    use AuthorizesPrint, HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:BoardingLeavePermit');
    }

    public function view(AuthUser $authUser, BoardingLeavePermit $boardingLeavePermit): bool
    {
        return $authUser->can('View:BoardingLeavePermit');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:BoardingLeavePermit');
    }

    public function update(AuthUser $authUser, BoardingLeavePermit $boardingLeavePermit): bool
    {
        return $authUser->can('Update:BoardingLeavePermit');
    }

    public function delete(AuthUser $authUser, BoardingLeavePermit $boardingLeavePermit): bool
    {
        return $authUser->can('Delete:BoardingLeavePermit');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:BoardingLeavePermit');
    }

    public function restore(AuthUser $authUser, BoardingLeavePermit $boardingLeavePermit): bool
    {
        return $authUser->can('Restore:BoardingLeavePermit');
    }

    public function forceDelete(AuthUser $authUser, BoardingLeavePermit $boardingLeavePermit): bool
    {
        return $authUser->can('ForceDelete:BoardingLeavePermit');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:BoardingLeavePermit');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:BoardingLeavePermit');
    }

    public function replicate(AuthUser $authUser, BoardingLeavePermit $boardingLeavePermit): bool
    {
        return $authUser->can('Replicate:BoardingLeavePermit');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:BoardingLeavePermit');
    }
}
