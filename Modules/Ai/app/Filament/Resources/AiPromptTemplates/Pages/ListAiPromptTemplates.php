<?php

namespace Modules\Ai\Filament\Resources\AiPromptTemplates\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Ai\Filament\Resources\AiPromptTemplates\AiPromptTemplateResource;

class ListAiPromptTemplates extends ListRecords
{
    protected static string $resource = AiPromptTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
