<?php

namespace Modules\Boarding\Filament\Resources\BoardingMealRecords\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Boarding\Filament\Resources\BoardingMealRecords\BoardingMealRecordResource;

class ListBoardingMealRecords extends ListRecords
{
    protected static string $resource = BoardingMealRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
