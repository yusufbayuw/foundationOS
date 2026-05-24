<?php

namespace Modules\Helpdesk\Filament\Resources\KnowledgeBaseArticles\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Helpdesk\Filament\Resources\KnowledgeBaseArticles\KnowledgeBaseArticleResource;

class ViewKnowledgeBaseArticle extends ViewRecord
{
    protected static string $resource = KnowledgeBaseArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
