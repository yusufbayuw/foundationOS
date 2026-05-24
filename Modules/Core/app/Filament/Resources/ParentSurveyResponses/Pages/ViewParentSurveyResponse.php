<?php

namespace Modules\Core\Filament\Resources\ParentSurveyResponses\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Core\Filament\Resources\ParentSurveyResponses\ParentSurveyResponseResource;

class ViewParentSurveyResponse extends ViewRecord
{
    protected static string $resource = ParentSurveyResponseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
