<?php

namespace Modules\Consulting\Filament\Resources\ConsultingEngagements\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Consulting\Filament\Resources\ConsultingEngagements\ConsultingEngagementResource;

class ListConsultingEngagements extends ListRecords
{
    protected static string $resource = ConsultingEngagementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
