<?php

namespace Modules\Exam\Filament\Resources\ExamTokens\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Exam\Filament\Resources\ExamTokens\ExamTokenResource;

class ListExamTokens extends ListRecords
{
    protected static string $resource = ExamTokenResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
