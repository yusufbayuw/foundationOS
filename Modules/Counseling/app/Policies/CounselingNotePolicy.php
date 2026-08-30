<?php

namespace Modules\Counseling\Policies;

use Modules\Core\Models\User;
use Modules\Counseling\Models\CounselingNote;

class CounselingNotePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:CounselingNote');
    }

    public function view(User $user, CounselingNote $note): bool
    {
        if ($user->isGlobalSuperAdmin()) {
            return true;
        }

        if (! $note->is_confidential) {
            return $user->can('View:CounselingNote');
        }

        return $user->can('View:CounselingNote')
            && $user->hasAnyRole(['counselor', 'principal', 'super_admin']);
    }

    public function create(User $user): bool
    {
        return $user->can('Create:CounselingNote');
    }

    public function update(User $user, CounselingNote $note): bool
    {
        return $user->can('Update:CounselingNote') && $this->view($user, $note);
    }

    public function delete(User $user, CounselingNote $note): bool
    {
        return $user->can('Delete:CounselingNote') && $this->view($user, $note);
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:CounselingNote');
    }

    public function restore(User $user, CounselingNote $note): bool
    {
        return $user->can('Restore:CounselingNote') && $this->view($user, $note);
    }

    public function forceDelete(User $user, CounselingNote $note): bool
    {
        return $user->can('ForceDelete:CounselingNote') && $this->view($user, $note);
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:CounselingNote');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:CounselingNote');
    }

    public function replicate(User $user, CounselingNote $note): bool
    {
        return $user->can('Replicate:CounselingNote') && $this->view($user, $note);
    }

    public function reorder(User $user): bool
    {
        return $user->can('Reorder:CounselingNote');
    }
}
