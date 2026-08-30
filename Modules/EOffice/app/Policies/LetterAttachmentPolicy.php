<?php

declare(strict_types=1);

namespace Modules\EOffice\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\EOffice\Models\LetterAttachment;

class LetterAttachmentPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:LetterAttachment');
    }

    public function view(AuthUser $authUser, LetterAttachment $letterAttachment): bool
    {
        return $authUser->can('View:LetterAttachment');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:LetterAttachment');
    }

    public function update(AuthUser $authUser, LetterAttachment $letterAttachment): bool
    {
        return $authUser->can('Update:LetterAttachment');
    }

    public function delete(AuthUser $authUser, LetterAttachment $letterAttachment): bool
    {
        return $authUser->can('Delete:LetterAttachment');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:LetterAttachment');
    }

    public function restore(AuthUser $authUser, LetterAttachment $letterAttachment): bool
    {
        return $authUser->can('Restore:LetterAttachment');
    }

    public function forceDelete(AuthUser $authUser, LetterAttachment $letterAttachment): bool
    {
        return $authUser->can('ForceDelete:LetterAttachment');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:LetterAttachment');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:LetterAttachment');
    }

    public function replicate(AuthUser $authUser, LetterAttachment $letterAttachment): bool
    {
        return $authUser->can('Replicate:LetterAttachment');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:LetterAttachment');
    }
}
