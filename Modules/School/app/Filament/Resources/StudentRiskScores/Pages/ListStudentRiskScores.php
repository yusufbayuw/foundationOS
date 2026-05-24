<?php

namespace Modules\School\Filament\Resources\StudentRiskScores\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\School\Filament\Resources\StudentRiskScores\StudentRiskScoreResource;

class ListStudentRiskScores extends ListRecords
{
    protected static string $resource = StudentRiskScoreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
