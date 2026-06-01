<?php

declare(strict_types=1);

namespace Modules\Campus\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Campus\Models\Wisuda;
use Modules\Core\Policies\Concerns\AuthorizesPrint;

class WisudaPolicy
{
    use AuthorizesPrint, HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Wisuda');
    }

    public function view(AuthUser $authUser, Wisuda $wisuda): bool
    {
        return $authUser->can('View:Wisuda');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Wisuda');
    }

    public function update(AuthUser $authUser, Wisuda $wisuda): bool
    {
        return $authUser->can('Update:Wisuda');
    }

    public function delete(AuthUser $authUser, Wisuda $wisuda): bool
    {
        return $authUser->can('Delete:Wisuda');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Wisuda');
    }

    public function restore(AuthUser $authUser, Wisuda $wisuda): bool
    {
        return $authUser->can('Restore:Wisuda');
    }

    public function forceDelete(AuthUser $authUser, Wisuda $wisuda): bool
    {
        return $authUser->can('ForceDelete:Wisuda');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Wisuda');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Wisuda');
    }

    public function replicate(AuthUser $authUser, Wisuda $wisuda): bool
    {
        return $authUser->can('Replicate:Wisuda');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Wisuda');
    }
}
