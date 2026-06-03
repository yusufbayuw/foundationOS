<?php

namespace Modules\Ai\Filament\Resources\AiPromptTemplates\Pages;

use Filament\Resources\Pages\EditRecord;
use Modules\Ai\Filament\Resources\AiPromptTemplates\AiPromptTemplateResource;

class EditAiPromptTemplate extends EditRecord
{
    protected static string $resource = AiPromptTemplateResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($data['code']) && is_string($data['code'])) {
            $data['code'] = strtoupper(trim($data['code']));
        }

        return $data;
    }
}
