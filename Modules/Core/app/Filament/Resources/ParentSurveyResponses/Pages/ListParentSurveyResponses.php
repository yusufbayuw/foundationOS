<?php

namespace Modules\Core\Filament\Resources\ParentSurveyResponses\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Core\Filament\Resources\ParentSurveyResponses\ParentSurveyResponseResource;

class ListParentSurveyResponses extends ListRecords
{
    protected static string $resource = ParentSurveyResponseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
