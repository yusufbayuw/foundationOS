<?php

namespace Modules\EducationQa\Filament\Resources\QualitySurveys\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\EducationQa\Filament\Resources\QualitySurveys\QualitySurveyResource;

class ViewQualitySurvey extends ViewRecord
{
    protected static string $resource = QualitySurveyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
