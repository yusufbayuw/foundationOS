<?php

namespace Modules\School\Filament\Resources\AssessmentItems\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\School\Filament\Resources\AssessmentItems\AssessmentItemResource;

class ListAssessmentItems extends ListRecords
{
    protected static string $resource = AssessmentItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
