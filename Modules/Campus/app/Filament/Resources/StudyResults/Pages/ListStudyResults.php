<?php

namespace Modules\Campus\Filament\Resources\StudyResults\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Campus\Filament\Resources\StudyResults\StudyResultResource;

class ListStudyResults extends ListRecords
{
    protected static string $resource = StudyResultResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
