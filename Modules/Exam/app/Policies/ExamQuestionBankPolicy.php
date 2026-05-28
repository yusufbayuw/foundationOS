<?php

declare(strict_types=1);

namespace Modules\Exam\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Exam\Models\ExamQuestionBank;

class ExamQuestionBankPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ExamQuestionBank');
    }

    public function view(AuthUser $authUser, ExamQuestionBank $examQuestionBank): bool
    {
        return $authUser->can('View:ExamQuestionBank');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ExamQuestionBank');
    }

    public function update(AuthUser $authUser, ExamQuestionBank $examQuestionBank): bool
    {
        return $authUser->can('Update:ExamQuestionBank');
    }

    public function delete(AuthUser $authUser, ExamQuestionBank $examQuestionBank): bool
    {
        return $authUser->can('Delete:ExamQuestionBank');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ExamQuestionBank');
    }

    public function restore(AuthUser $authUser, ExamQuestionBank $examQuestionBank): bool
    {
        return $authUser->can('Restore:ExamQuestionBank');
    }

    public function forceDelete(AuthUser $authUser, ExamQuestionBank $examQuestionBank): bool
    {
        return $authUser->can('ForceDelete:ExamQuestionBank');
    }
}
