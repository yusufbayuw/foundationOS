<?php

namespace Modules\Counseling\Filament\Resources\WellbeingSurveys\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Counseling\Filament\Resources\WellbeingSurveys\WellbeingSurveyResource;

class ViewWellbeingSurvey extends ViewRecord
{
    protected static string $resource = WellbeingSurveyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
