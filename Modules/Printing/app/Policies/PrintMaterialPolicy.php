<?php

declare(strict_types=1);

namespace Modules\Printing\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Printing\Models\PrintMaterial;

class PrintMaterialPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PrintMaterial');
    }

    public function view(AuthUser $authUser, PrintMaterial $printMaterial): bool
    {
        return $authUser->can('View:PrintMaterial');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PrintMaterial');
    }

    public function update(AuthUser $authUser, PrintMaterial $printMaterial): bool
    {
        return $authUser->can('Update:PrintMaterial');
    }

    public function delete(AuthUser $authUser, PrintMaterial $printMaterial): bool
    {
        return $authUser->can('Delete:PrintMaterial');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PrintMaterial');
    }

    public function restore(AuthUser $authUser, PrintMaterial $printMaterial): bool
    {
        return $authUser->can('Restore:PrintMaterial');
    }

    public function forceDelete(AuthUser $authUser, PrintMaterial $printMaterial): bool
    {
        return $authUser->can('ForceDelete:PrintMaterial');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PrintMaterial');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PrintMaterial');
    }

    public function replicate(AuthUser $authUser, PrintMaterial $printMaterial): bool
    {
        return $authUser->can('Replicate:PrintMaterial');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PrintMaterial');
    }
}
