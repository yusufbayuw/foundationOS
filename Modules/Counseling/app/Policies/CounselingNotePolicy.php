<?php

namespace Modules\Counseling\Policies;

use Modules\Core\Models\User;
use Modules\Counseling\Models\CounselingNote;

class CounselingNotePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isGlobalSuperAdmin() || $user->userTenantRoles()->exists();
    }

    public function view(User $user, CounselingNote $note): bool
    {
        if (! $note->is_confidential) {
            return $this->viewAny($user);
        }

        if ($user->isGlobalSuperAdmin()) {
            return true;
        }

        return $user->hasAnyRole(['counselor', 'principal', 'super_admin']);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, CounselingNote $note): bool
    {
        return $this->view($user, $note);
    }

    public function delete(User $user, CounselingNote $note): bool
    {
        return $this->view($user, $note);
    }
}
