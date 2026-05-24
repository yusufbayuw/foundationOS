<?php

namespace Modules\Core\Filament\Resources\ParentSurveys\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Core\Filament\Resources\ParentSurveys\ParentSurveyResource;

class ViewParentSurvey extends ViewRecord
{
    protected static string $resource = ParentSurveyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
