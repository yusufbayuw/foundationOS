<?php

namespace Modules\Boarding\Filament\Resources\BoardingMealRecords\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Boarding\Filament\Resources\BoardingMealRecords\BoardingMealRecordResource;

class ViewBoardingMealRecord extends ViewRecord
{
    protected static string $resource = BoardingMealRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
