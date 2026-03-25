<?php

namespace Modules\Campus\Filament\Resources\StudyPlans\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Campus\Filament\Resources\StudyPlans\StudyPlanResource;

class ListStudyPlans extends ListRecords
{
    protected static string $resource = StudyPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
