<?php

namespace Modules\Helpdesk\Filament\Resources\KnowledgeBaseArticles\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Helpdesk\Filament\Resources\KnowledgeBaseArticles\KnowledgeBaseArticleResource;

class ListKnowledgeBaseArticles extends ListRecords
{
    protected static string $resource = KnowledgeBaseArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
