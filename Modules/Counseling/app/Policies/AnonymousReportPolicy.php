<?php

declare(strict_types=1);

namespace Modules\Counseling\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Counseling\Models\AnonymousReport;

class AnonymousReportPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AnonymousReport');
    }

    public function view(AuthUser $authUser, AnonymousReport $anonymousReport): bool
    {
        return $authUser->can('View:AnonymousReport');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AnonymousReport');
    }

    public function update(AuthUser $authUser, AnonymousReport $anonymousReport): bool
    {
        return $authUser->can('Update:AnonymousReport');
    }

    public function delete(AuthUser $authUser, AnonymousReport $anonymousReport): bool
    {
        return $authUser->can('Delete:AnonymousReport');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:AnonymousReport');
    }

    public function restore(AuthUser $authUser, AnonymousReport $anonymousReport): bool
    {
        return $authUser->can('Restore:AnonymousReport');
    }

    public function forceDelete(AuthUser $authUser, AnonymousReport $anonymousReport): bool
    {
        return $authUser->can('ForceDelete:AnonymousReport');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:AnonymousReport');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:AnonymousReport');
    }

    public function replicate(AuthUser $authUser, AnonymousReport $anonymousReport): bool
    {
        return $authUser->can('Replicate:AnonymousReport');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:AnonymousReport');
    }
}
