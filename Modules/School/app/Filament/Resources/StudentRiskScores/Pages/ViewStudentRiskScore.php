<?php

namespace Modules\School\Filament\Resources\StudentRiskScores\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\School\Filament\Resources\StudentRiskScores\StudentRiskScoreResource;

class ViewStudentRiskScore extends ViewRecord
{
    protected static string $resource = StudentRiskScoreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
