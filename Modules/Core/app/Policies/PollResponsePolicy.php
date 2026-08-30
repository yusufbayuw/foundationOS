<?php

declare(strict_types=1);

namespace Modules\Core\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Core\Models\PollResponse;

class PollResponsePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PollResponse');
    }

    public function view(AuthUser $authUser, PollResponse $pollResponse): bool
    {
        return $authUser->can('View:PollResponse');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PollResponse');
    }

    public function update(AuthUser $authUser, PollResponse $pollResponse): bool
    {
        return $authUser->can('Update:PollResponse');
    }

    public function delete(AuthUser $authUser, PollResponse $pollResponse): bool
    {
        return $authUser->can('Delete:PollResponse');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PollResponse');
    }

    public function restore(AuthUser $authUser, PollResponse $pollResponse): bool
    {
        return $authUser->can('Restore:PollResponse');
    }

    public function forceDelete(AuthUser $authUser, PollResponse $pollResponse): bool
    {
        return $authUser->can('ForceDelete:PollResponse');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PollResponse');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PollResponse');
    }

    public function replicate(AuthUser $authUser, PollResponse $pollResponse): bool
    {
        return $authUser->can('Replicate:PollResponse');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PollResponse');
    }
}
