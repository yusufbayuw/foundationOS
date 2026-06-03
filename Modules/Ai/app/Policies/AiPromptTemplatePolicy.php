<?php

declare(strict_types=1);

namespace Modules\Ai\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Ai\Models\AiPromptTemplate;

class AiPromptTemplatePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AiPromptTemplate');
    }

    public function view(AuthUser $authUser, AiPromptTemplate $aiPromptTemplate): bool
    {
        return $authUser->can('View:AiPromptTemplate');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AiPromptTemplate');
    }

    public function update(AuthUser $authUser, AiPromptTemplate $aiPromptTemplate): bool
    {
        return $authUser->can('Update:AiPromptTemplate');
    }

    public function delete(AuthUser $authUser, AiPromptTemplate $aiPromptTemplate): bool
    {
        return $authUser->can('Delete:AiPromptTemplate');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:AiPromptTemplate');
    }

    public function restore(AuthUser $authUser, AiPromptTemplate $aiPromptTemplate): bool
    {
        return $authUser->can('Restore:AiPromptTemplate');
    }

    public function forceDelete(AuthUser $authUser, AiPromptTemplate $aiPromptTemplate): bool
    {
        return $authUser->can('ForceDelete:AiPromptTemplate');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:AiPromptTemplate');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:AiPromptTemplate');
    }

    public function replicate(AuthUser $authUser, AiPromptTemplate $aiPromptTemplate): bool
    {
        return $authUser->can('Replicate:AiPromptTemplate');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:AiPromptTemplate');
    }
}
