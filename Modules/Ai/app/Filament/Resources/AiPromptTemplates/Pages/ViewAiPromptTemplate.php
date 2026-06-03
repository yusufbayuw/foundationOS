<?php

namespace Modules\Ai\Filament\Resources\AiPromptTemplates\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Ai\Filament\Resources\AiPromptTemplates\AiPromptTemplateResource;

class ViewAiPromptTemplate extends ViewRecord
{
    protected static string $resource = AiPromptTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
