<?php

namespace Modules\Consulting\Filament\Resources\ConsultingClients\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Consulting\Filament\Resources\ConsultingClients\ConsultingClientResource;

class EditConsultingClient extends EditRecord
{
    protected static string $resource = ConsultingClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
