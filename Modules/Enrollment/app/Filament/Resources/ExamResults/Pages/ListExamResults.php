<?php

namespace Modules\Enrollment\Filament\Resources\ExamResults\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Enrollment\Filament\Resources\ExamResults\ExamResultResource;

class ListExamResults extends ListRecords
{
    protected static string $resource = ExamResultResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
