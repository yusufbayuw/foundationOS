<?php

namespace Modules\Exam\Filament\Resources\ExamParticipants\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Exam\Filament\Resources\ExamParticipants\ExamParticipantResource;

class ListExamParticipants extends ListRecords
{
    protected static string $resource = ExamParticipantResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
