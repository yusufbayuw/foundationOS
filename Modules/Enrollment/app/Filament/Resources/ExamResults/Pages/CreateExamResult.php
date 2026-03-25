<?php

namespace Modules\Enrollment\Filament\Resources\ExamResults\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Enrollment\Filament\Resources\ExamResults\ExamResultResource;

class CreateExamResult extends CreateRecord
{
    protected static string $resource = ExamResultResource::class;
}
