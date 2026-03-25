<?php

namespace Modules\School\Filament\Resources\AssessmentItems\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\School\Filament\Resources\AssessmentItems\AssessmentItemResource;

class ViewAssessmentItem extends ViewRecord
{
    protected static string $resource = AssessmentItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
