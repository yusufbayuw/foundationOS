<?php

namespace Modules\Exam\Filament\Resources\ExamTokens\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Exam\Filament\Resources\ExamTokens\ExamTokenResource;

class CreateExamToken extends CreateRecord
{
    protected static string $resource = ExamTokenResource::class;
}
