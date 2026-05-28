<?php

declare(strict_types=1);

namespace Modules\Exam\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Exam\Models\ExamToken;

class ExamTokenPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ExamToken');
    }

    public function view(AuthUser $authUser, ExamToken $examToken): bool
    {
        return $authUser->can('View:ExamToken');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ExamToken');
    }

    public function update(AuthUser $authUser, ExamToken $examToken): bool
    {
        return $authUser->can('Update:ExamToken');
    }

    public function delete(AuthUser $authUser, ExamToken $examToken): bool
    {
        return $authUser->can('Delete:ExamToken');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ExamToken');
    }

    public function restore(AuthUser $authUser, ExamToken $examToken): bool
    {
        return $authUser->can('Restore:ExamToken');
    }

    public function forceDelete(AuthUser $authUser, ExamToken $examToken): bool
    {
        return $authUser->can('ForceDelete:ExamToken');
    }
}
