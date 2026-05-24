<?php

namespace Modules\EducationQa\Filament\Resources\QualitySurveys\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\EducationQa\Filament\Resources\QualitySurveys\QualitySurveyResource;

class ListQualitySurveys extends ListRecords
{
    protected static string $resource = QualitySurveyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
