<?php

declare(strict_types=1);

namespace Modules\EOffice\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\EOffice\Models\LetterCategory;

class LetterCategoryPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:LetterCategory');
    }

    public function view(AuthUser $authUser, LetterCategory $letterCategory): bool
    {
        return $authUser->can('View:LetterCategory');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:LetterCategory');
    }

    public function update(AuthUser $authUser, LetterCategory $letterCategory): bool
    {
        return $authUser->can('Update:LetterCategory');
    }

    public function delete(AuthUser $authUser, LetterCategory $letterCategory): bool
    {
        return $authUser->can('Delete:LetterCategory');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:LetterCategory');
    }

    public function restore(AuthUser $authUser, LetterCategory $letterCategory): bool
    {
        return $authUser->can('Restore:LetterCategory');
    }

    public function forceDelete(AuthUser $authUser, LetterCategory $letterCategory): bool
    {
        return $authUser->can('ForceDelete:LetterCategory');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:LetterCategory');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:LetterCategory');
    }

    public function replicate(AuthUser $authUser, LetterCategory $letterCategory): bool
    {
        return $authUser->can('Replicate:LetterCategory');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:LetterCategory');
    }
}
