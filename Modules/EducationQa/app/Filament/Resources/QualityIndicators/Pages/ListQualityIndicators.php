<?php

namespace Modules\EducationQa\Filament\Resources\QualityIndicators\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\EducationQa\Filament\Resources\QualityIndicators\QualityIndicatorResource;

class ListQualityIndicators extends ListRecords
{
    protected static string $resource = QualityIndicatorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
