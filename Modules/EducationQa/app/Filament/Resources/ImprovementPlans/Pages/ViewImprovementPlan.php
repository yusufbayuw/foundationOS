<?php

namespace Modules\EducationQa\Filament\Resources\ImprovementPlans\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\EducationQa\Filament\Resources\ImprovementPlans\ImprovementPlanResource;

class ViewImprovementPlan extends ViewRecord
{
    protected static string $resource = ImprovementPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
