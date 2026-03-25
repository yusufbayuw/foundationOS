<?php

namespace Modules\Campus\Filament\Resources\FeederLogs\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Campus\Filament\Resources\FeederLogs\FeederLogResource;

class ViewFeederLog extends ViewRecord
{
    protected static string $resource = FeederLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
