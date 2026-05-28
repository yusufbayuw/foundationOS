<?php

namespace Modules\Exam\Filament\Resources\ExamQuestionBanks\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Exam\Filament\Resources\ExamQuestionBanks\ExamQuestionBankResource;

class CreateExamQuestionBank extends CreateRecord
{
    protected static string $resource = ExamQuestionBankResource::class;
}
