<?php

namespace Modules\Campus\Filament\Resources\StudyPlans\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Campus\Filament\Resources\StudyPlans\StudyPlanResource;

class ViewStudyPlan extends ViewRecord
{
    protected static string $resource = StudyPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
