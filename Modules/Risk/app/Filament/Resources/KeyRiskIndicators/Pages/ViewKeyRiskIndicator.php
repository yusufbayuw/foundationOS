<?php

namespace Modules\Risk\Filament\Resources\KeyRiskIndicators\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Risk\Filament\Resources\KeyRiskIndicators\KeyRiskIndicatorResource;

class ViewKeyRiskIndicator extends ViewRecord
{
    protected static string $resource = KeyRiskIndicatorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
