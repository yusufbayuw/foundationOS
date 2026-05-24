<?php

namespace Modules\IsoCompliance\Filament\Resources\IsoIncidents\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\IsoCompliance\Filament\Resources\IsoIncidents\IsoIncidentResource;

class EditIsoIncident extends EditRecord
{
    protected static string $resource = IsoIncidentResource::class;

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
