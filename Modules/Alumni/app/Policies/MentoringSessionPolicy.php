<?php

declare(strict_types=1);

namespace Modules\Alumni\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Alumni\Models\MentoringSession;

class MentoringSessionPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:MentoringSession');
    }

    public function view(AuthUser $authUser, MentoringSession $mentoringSession): bool
    {
        return $authUser->can('View:MentoringSession');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:MentoringSession');
    }

    public function update(AuthUser $authUser, MentoringSession $mentoringSession): bool
    {
        return $authUser->can('Update:MentoringSession');
    }

    public function delete(AuthUser $authUser, MentoringSession $mentoringSession): bool
    {
        return $authUser->can('Delete:MentoringSession');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:MentoringSession');
    }

    public function restore(AuthUser $authUser, MentoringSession $mentoringSession): bool
    {
        return $authUser->can('Restore:MentoringSession');
    }

    public function forceDelete(AuthUser $authUser, MentoringSession $mentoringSession): bool
    {
        return $authUser->can('ForceDelete:MentoringSession');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:MentoringSession');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:MentoringSession');
    }

    public function replicate(AuthUser $authUser, MentoringSession $mentoringSession): bool
    {
        return $authUser->can('Replicate:MentoringSession');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:MentoringSession');
    }
}
