<?php

namespace Modules\Facility\Filament\Resources\UtilityReadings\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Facility\Filament\Resources\UtilityReadings\UtilityReadingResource;

class ViewUtilityReading extends ViewRecord
{
    protected static string $resource = UtilityReadingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
