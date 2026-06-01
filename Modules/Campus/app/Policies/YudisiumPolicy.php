<?php

declare(strict_types=1);

namespace Modules\Campus\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Campus\Models\Yudisium;
use Modules\Core\Policies\Concerns\AuthorizesPrint;

class YudisiumPolicy
{
    use AuthorizesPrint, HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Yudisium');
    }

    public function view(AuthUser $authUser, Yudisium $yudisium): bool
    {
        return $authUser->can('View:Yudisium');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Yudisium');
    }

    public function update(AuthUser $authUser, Yudisium $yudisium): bool
    {
        return $authUser->can('Update:Yudisium');
    }

    public function delete(AuthUser $authUser, Yudisium $yudisium): bool
    {
        return $authUser->can('Delete:Yudisium');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Yudisium');
    }

    public function restore(AuthUser $authUser, Yudisium $yudisium): bool
    {
        return $authUser->can('Restore:Yudisium');
    }

    public function forceDelete(AuthUser $authUser, Yudisium $yudisium): bool
    {
        return $authUser->can('ForceDelete:Yudisium');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Yudisium');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Yudisium');
    }

    public function replicate(AuthUser $authUser, Yudisium $yudisium): bool
    {
        return $authUser->can('Replicate:Yudisium');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Yudisium');
    }
}
