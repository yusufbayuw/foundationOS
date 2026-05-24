<?php

namespace Modules\Consulting\Filament\Resources\ConsultingClients\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Consulting\Filament\Resources\ConsultingClients\ConsultingClientResource;

class ViewConsultingClient extends ViewRecord
{
    protected static string $resource = ConsultingClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
