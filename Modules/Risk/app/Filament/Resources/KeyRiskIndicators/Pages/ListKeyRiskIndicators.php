<?php

namespace Modules\Risk\Filament\Resources\KeyRiskIndicators\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Risk\Filament\Resources\KeyRiskIndicators\KeyRiskIndicatorResource;

class ListKeyRiskIndicators extends ListRecords
{
    protected static string $resource = KeyRiskIndicatorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
