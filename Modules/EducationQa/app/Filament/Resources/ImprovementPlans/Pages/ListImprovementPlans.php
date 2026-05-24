<?php

namespace Modules\EducationQa\Filament\Resources\ImprovementPlans\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\EducationQa\Filament\Resources\ImprovementPlans\ImprovementPlanResource;

class ListImprovementPlans extends ListRecords
{
    protected static string $resource = ImprovementPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
