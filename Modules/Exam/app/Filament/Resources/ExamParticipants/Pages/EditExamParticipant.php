<?php

namespace Modules\Exam\Filament\Resources\ExamParticipants\Pages;

use Filament\Resources\Pages\EditRecord;
use Modules\Exam\Filament\Resources\ExamParticipants\ExamParticipantResource;

class EditExamParticipant extends EditRecord
{
    protected static string $resource = ExamParticipantResource::class;
}
