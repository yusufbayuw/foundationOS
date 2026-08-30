<?php

declare(strict_types=1);

namespace Modules\Printing\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Printing\Models\PrintTemplate;

class PrintTemplatePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PrintTemplate');
    }

    public function view(AuthUser $authUser, PrintTemplate $printTemplate): bool
    {
        return $authUser->can('View:PrintTemplate');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PrintTemplate');
    }

    public function update(AuthUser $authUser, PrintTemplate $printTemplate): bool
    {
        return $authUser->can('Update:PrintTemplate');
    }

    public function delete(AuthUser $authUser, PrintTemplate $printTemplate): bool
    {
        return $authUser->can('Delete:PrintTemplate');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PrintTemplate');
    }

    public function restore(AuthUser $authUser, PrintTemplate $printTemplate): bool
    {
        return $authUser->can('Restore:PrintTemplate');
    }

    public function forceDelete(AuthUser $authUser, PrintTemplate $printTemplate): bool
    {
        return $authUser->can('ForceDelete:PrintTemplate');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PrintTemplate');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PrintTemplate');
    }

    public function replicate(AuthUser $authUser, PrintTemplate $printTemplate): bool
    {
        return $authUser->can('Replicate:PrintTemplate');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PrintTemplate');
    }
}
