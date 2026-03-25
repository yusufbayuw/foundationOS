<?php

namespace Modules\Campus\Filament\Resources\StudyPlanItems\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Campus\Filament\Resources\StudyPlanItems\StudyPlanItemResource;

class ListStudyPlanItems extends ListRecords
{
    protected static string $resource = StudyPlanItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
