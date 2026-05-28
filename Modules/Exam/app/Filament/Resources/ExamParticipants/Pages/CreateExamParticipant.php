<?php

namespace Modules\Exam\Filament\Resources\ExamParticipants\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Exam\Filament\Resources\ExamParticipants\ExamParticipantResource;

class CreateExamParticipant extends CreateRecord
{
    protected static string $resource = ExamParticipantResource::class;
}
