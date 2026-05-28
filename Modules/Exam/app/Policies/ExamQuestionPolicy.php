<?php

declare(strict_types=1);

namespace Modules\Exam\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Exam\Models\ExamQuestion;

class ExamQuestionPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ExamQuestion');
    }

    public function view(AuthUser $authUser, ExamQuestion $examQuestion): bool
    {
        return $authUser->can('View:ExamQuestion');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ExamQuestion');
    }

    public function update(AuthUser $authUser, ExamQuestion $examQuestion): bool
    {
        return $authUser->can('Update:ExamQuestion');
    }

    public function delete(AuthUser $authUser, ExamQuestion $examQuestion): bool
    {
        return $authUser->can('Delete:ExamQuestion');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ExamQuestion');
    }

    public function restore(AuthUser $authUser, ExamQuestion $examQuestion): bool
    {
        return $authUser->can('Restore:ExamQuestion');
    }

    public function forceDelete(AuthUser $authUser, ExamQuestion $examQuestion): bool
    {
        return $authUser->can('ForceDelete:ExamQuestion');
    }
}
