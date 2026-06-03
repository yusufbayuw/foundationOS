<?php

namespace Modules\Ai\Filament\Resources\AiPromptTemplates\Pages;

use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Modules\Ai\Filament\Resources\AiPromptTemplates\AiPromptTemplateResource;
use Modules\Ai\Services\AiPromptTemplateRegistrationService;

class CreateAiPromptTemplate extends CreateRecord
{
    protected static string $resource = AiPromptTemplateResource::class;

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordCreation(array $data): Model
    {
        $tenant = Filament::getTenant();

        return app(AiPromptTemplateRegistrationService::class)->register(
            tenantId: (int) $tenant?->getKey(),
            organizationId: isset($data['organization_id']) ? (int) $data['organization_id'] : null,
            code: (string) $data['code'],
            name: (string) $data['name'],
            description: $data['description'] ?? null,
            meta: is_array($data['meta'] ?? null) ? $data['meta'] : [],
            status: (string) ($data['status'] ?? 'active'),
        );
    }
}
