<?php

namespace Modules\EducationQa\Filament\Resources\QualityIndicators\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\EducationQa\Filament\Resources\QualityIndicators\QualityIndicatorResource;

class EditQualityIndicator extends EditRecord
{
    protected static string $resource = QualityIndicatorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
