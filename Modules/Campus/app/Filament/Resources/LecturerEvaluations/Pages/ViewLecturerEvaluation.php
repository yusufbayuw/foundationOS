<?php

namespace Modules\Campus\Filament\Resources\LecturerEvaluations\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Campus\Filament\Resources\LecturerEvaluations\LecturerEvaluationResource;

class ViewLecturerEvaluation extends ViewRecord
{
    protected static string $resource = LecturerEvaluationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
