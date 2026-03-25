<?php

namespace Modules\Campus\Filament\Resources\StudyResults\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Campus\Filament\Resources\StudyResults\StudyResultResource;

class ViewStudyResult extends ViewRecord
{
    protected static string $resource = StudyResultResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
