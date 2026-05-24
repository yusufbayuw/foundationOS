<?php

namespace Modules\EducationQa\Filament\Resources\GapAnalyses\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\EducationQa\Filament\Resources\GapAnalyses\GapAnalysisResource;

class ListGapAnalyses extends ListRecords
{
    protected static string $resource = GapAnalysisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
