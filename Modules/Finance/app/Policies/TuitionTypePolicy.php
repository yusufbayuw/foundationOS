<?php

declare(strict_types=1);

namespace Modules\Finance\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Finance\Models\TuitionType;

class TuitionTypePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:TuitionType');
    }

    public function view(AuthUser $authUser, TuitionType $tuitionType): bool
    {
        return $authUser->can('View:TuitionType');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:TuitionType');
    }

    public function update(AuthUser $authUser, TuitionType $tuitionType): bool
    {
        return $authUser->can('Update:TuitionType');
    }

    public function delete(AuthUser $authUser, TuitionType $tuitionType): bool
    {
        return $authUser->can('Delete:TuitionType');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:TuitionType');
    }

    public function restore(AuthUser $authUser, TuitionType $tuitionType): bool
    {
        return $authUser->can('Restore:TuitionType');
    }

    public function forceDelete(AuthUser $authUser, TuitionType $tuitionType): bool
    {
        return $authUser->can('ForceDelete:TuitionType');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:TuitionType');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:TuitionType');
    }

    public function replicate(AuthUser $authUser, TuitionType $tuitionType): bool
    {
        return $authUser->can('Replicate:TuitionType');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:TuitionType');
    }
}
