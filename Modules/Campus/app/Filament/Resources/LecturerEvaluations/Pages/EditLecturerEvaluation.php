<?php

namespace Modules\Campus\Filament\Resources\LecturerEvaluations\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Campus\Filament\Resources\LecturerEvaluations\LecturerEvaluationResource;

class EditLecturerEvaluation extends EditRecord
{
    protected static string $resource = LecturerEvaluationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
