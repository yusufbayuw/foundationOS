<?php

namespace Modules\Consulting\Filament\Resources\ConsultingEngagements\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Consulting\Filament\Resources\ConsultingEngagements\ConsultingEngagementResource;

class ViewConsultingEngagement extends ViewRecord
{
    protected static string $resource = ConsultingEngagementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
