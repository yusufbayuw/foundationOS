<?php

namespace Modules\Consulting\Filament\Resources\ConsultingClients\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Consulting\Filament\Resources\ConsultingClients\ConsultingClientResource;

class ListConsultingClients extends ListRecords
{
    protected static string $resource = ConsultingClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
