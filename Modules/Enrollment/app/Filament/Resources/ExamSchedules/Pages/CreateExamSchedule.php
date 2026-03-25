<?php

namespace Modules\Enrollment\Filament\Resources\ExamSchedules\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Enrollment\Filament\Resources\ExamSchedules\ExamScheduleResource;

class CreateExamSchedule extends CreateRecord
{
    protected static string $resource = ExamScheduleResource::class;
}
