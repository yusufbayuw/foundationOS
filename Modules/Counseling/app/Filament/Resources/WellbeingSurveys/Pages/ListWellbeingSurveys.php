<?php

namespace Modules\Counseling\Filament\Resources\WellbeingSurveys\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Counseling\Filament\Resources\WellbeingSurveys\WellbeingSurveyResource;

class ListWellbeingSurveys extends ListRecords
{
    protected static string $resource = WellbeingSurveyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
