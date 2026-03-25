<?php

namespace Modules\School\Filament\Resources\ClassStudents\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\School\Filament\Resources\ClassStudents\ClassStudentResource;

class ListClassStudents extends ListRecords
{
    protected static string $resource = ClassStudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
