<?php

namespace Modules\EducationQa\Filament\Resources\GapAnalyses\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\EducationQa\Filament\Resources\GapAnalyses\GapAnalysisResource;

class ViewGapAnalysis extends ViewRecord
{
    protected static string $resource = GapAnalysisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
