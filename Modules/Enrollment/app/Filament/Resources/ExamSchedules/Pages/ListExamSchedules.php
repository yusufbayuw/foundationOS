<?php

namespace Modules\Enrollment\Filament\Resources\ExamSchedules\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Enrollment\Filament\Resources\ExamSchedules\ExamScheduleResource;

class ListExamSchedules extends ListRecords
{
    protected static string $resource = ExamScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
