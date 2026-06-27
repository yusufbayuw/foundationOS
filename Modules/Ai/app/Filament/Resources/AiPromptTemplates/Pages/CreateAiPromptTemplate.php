<?php

namespace Modules\Ai\Filament\Resources\AiPromptTemplates\Pages;

use App\Support\TypedValue;
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
        $tenantId = TypedValue::int($tenant?->getKey());
        $description = isset($data['description']) ? TypedValue::string($data['description']) : null;
        $meta = is_array($data['meta'] ?? null) ? $data['meta'] : [];
        /** @var array<string, mixed> $meta */

        return app(AiPromptTemplateRegistrationService::class)->register(
            tenantId: $tenantId,
            organizationId: isset($data['organization_id']) ? TypedValue::int($data['organization_id']) : null,
            code: TypedValue::string($data['code']),
            name: TypedValue::string($data['name']),
            description: $description,
            meta: $meta,
            status: TypedValue::string($data['status'] ?? 'active'),
        );
    }
}
