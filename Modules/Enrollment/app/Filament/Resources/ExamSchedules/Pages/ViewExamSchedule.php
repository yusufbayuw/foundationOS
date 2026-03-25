<?php

namespace Modules\Enrollment\Filament\Resources\ExamSchedules\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Enrollment\Filament\Resources\ExamSchedules\ExamScheduleResource;

class ViewExamSchedule extends ViewRecord
{
    protected static string $resource = ExamScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
