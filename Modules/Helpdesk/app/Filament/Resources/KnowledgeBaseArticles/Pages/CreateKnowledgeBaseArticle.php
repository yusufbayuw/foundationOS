<?php

namespace Modules\Helpdesk\Filament\Resources\KnowledgeBaseArticles\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Helpdesk\Filament\Resources\KnowledgeBaseArticles\KnowledgeBaseArticleResource;

class CreateKnowledgeBaseArticle extends CreateRecord
{
    protected static string $resource = KnowledgeBaseArticleResource::class;
}
