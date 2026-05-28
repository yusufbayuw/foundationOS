<?php

namespace Modules\Exam\Filament\Resources\ExamQuestions\Pages;

use Filament\Resources\Pages\ViewRecord;
use Modules\Exam\Filament\Resources\ExamQuestions\ExamQuestionResource;

class ViewExamQuestion extends ViewRecord
{
    protected static string $resource = ExamQuestionResource::class;
}
