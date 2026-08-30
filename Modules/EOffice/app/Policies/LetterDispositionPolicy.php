<?php

declare(strict_types=1);

namespace Modules\EOffice\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\EOffice\Models\LetterDisposition;

class LetterDispositionPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:LetterDisposition');
    }

    public function view(AuthUser $authUser, LetterDisposition $letterDisposition): bool
    {
        return $authUser->can('View:LetterDisposition');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:LetterDisposition');
    }

    public function update(AuthUser $authUser, LetterDisposition $letterDisposition): bool
    {
        return $authUser->can('Update:LetterDisposition');
    }

    public function delete(AuthUser $authUser, LetterDisposition $letterDisposition): bool
    {
        return $authUser->can('Delete:LetterDisposition');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:LetterDisposition');
    }

    public function restore(AuthUser $authUser, LetterDisposition $letterDisposition): bool
    {
        return $authUser->can('Restore:LetterDisposition');
    }

    public function forceDelete(AuthUser $authUser, LetterDisposition $letterDisposition): bool
    {
        return $authUser->can('ForceDelete:LetterDisposition');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:LetterDisposition');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:LetterDisposition');
    }

    public function replicate(AuthUser $authUser, LetterDisposition $letterDisposition): bool
    {
        return $authUser->can('Replicate:LetterDisposition');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:LetterDisposition');
    }
}
