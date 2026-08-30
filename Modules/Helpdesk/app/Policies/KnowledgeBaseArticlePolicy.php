<?php

declare(strict_types=1);

namespace Modules\Helpdesk\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Helpdesk\Models\KnowledgeBaseArticle;

class KnowledgeBaseArticlePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:KnowledgeBaseArticle');
    }

    public function view(AuthUser $authUser, KnowledgeBaseArticle $knowledgeBaseArticle): bool
    {
        return $authUser->can('View:KnowledgeBaseArticle');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:KnowledgeBaseArticle');
    }

    public function update(AuthUser $authUser, KnowledgeBaseArticle $knowledgeBaseArticle): bool
    {
        return $authUser->can('Update:KnowledgeBaseArticle');
    }

    public function delete(AuthUser $authUser, KnowledgeBaseArticle $knowledgeBaseArticle): bool
    {
        return $authUser->can('Delete:KnowledgeBaseArticle');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:KnowledgeBaseArticle');
    }

    public function restore(AuthUser $authUser, KnowledgeBaseArticle $knowledgeBaseArticle): bool
    {
        return $authUser->can('Restore:KnowledgeBaseArticle');
    }

    public function forceDelete(AuthUser $authUser, KnowledgeBaseArticle $knowledgeBaseArticle): bool
    {
        return $authUser->can('ForceDelete:KnowledgeBaseArticle');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:KnowledgeBaseArticle');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:KnowledgeBaseArticle');
    }

    public function replicate(AuthUser $authUser, KnowledgeBaseArticle $knowledgeBaseArticle): bool
    {
        return $authUser->can('Replicate:KnowledgeBaseArticle');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:KnowledgeBaseArticle');
    }
}
