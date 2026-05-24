<?php

namespace Modules\EducationQa\Filament\Resources\QualityIndicators\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\EducationQa\Filament\Resources\QualityIndicators\QualityIndicatorResource;

class ViewQualityIndicator extends ViewRecord
{
    protected static string $resource = QualityIndicatorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
