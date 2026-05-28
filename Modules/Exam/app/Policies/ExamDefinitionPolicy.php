<?php

declare(strict_types=1);

namespace Modules\Exam\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Exam\Models\ExamDefinition;

class ExamDefinitionPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ExamDefinition');
    }

    public function view(AuthUser $authUser, ExamDefinition $examDefinition): bool
    {
        return $authUser->can('View:ExamDefinition');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ExamDefinition');
    }

    public function update(AuthUser $authUser, ExamDefinition $examDefinition): bool
    {
        return $authUser->can('Update:ExamDefinition');
    }

    public function delete(AuthUser $authUser, ExamDefinition $examDefinition): bool
    {
        return $authUser->can('Delete:ExamDefinition');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ExamDefinition');
    }

    public function restore(AuthUser $authUser, ExamDefinition $examDefinition): bool
    {
        return $authUser->can('Restore:ExamDefinition');
    }

    public function forceDelete(AuthUser $authUser, ExamDefinition $examDefinition): bool
    {
        return $authUser->can('ForceDelete:ExamDefinition');
    }

    public function replicate(AuthUser $authUser, ExamDefinition $examDefinition): bool
    {
        return $authUser->can('Replicate:ExamDefinition');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ExamDefinition');
    }
}
