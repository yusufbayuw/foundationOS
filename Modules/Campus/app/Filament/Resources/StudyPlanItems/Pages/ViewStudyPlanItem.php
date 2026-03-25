<?php

namespace Modules\Campus\Filament\Resources\StudyPlanItems\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Campus\Filament\Resources\StudyPlanItems\StudyPlanItemResource;

class ViewStudyPlanItem extends ViewRecord
{
    protected static string $resource = StudyPlanItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
