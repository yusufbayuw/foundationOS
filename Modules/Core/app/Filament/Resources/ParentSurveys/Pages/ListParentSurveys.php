<?php

namespace Modules\Core\Filament\Resources\ParentSurveys\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Core\Filament\Resources\ParentSurveys\ParentSurveyResource;

class ListParentSurveys extends ListRecords
{
    protected static string $resource = ParentSurveyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
