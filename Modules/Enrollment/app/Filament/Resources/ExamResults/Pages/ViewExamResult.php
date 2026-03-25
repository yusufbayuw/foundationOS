<?php

namespace Modules\Enrollment\Filament\Resources\ExamResults\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Enrollment\Filament\Resources\ExamResults\ExamResultResource;

class ViewExamResult extends ViewRecord
{
    protected static string $resource = ExamResultResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
