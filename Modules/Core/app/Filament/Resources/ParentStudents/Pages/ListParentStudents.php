<?php

namespace Modules\Core\Filament\Resources\ParentStudents\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Core\Filament\Resources\ParentStudents\ParentStudentResource;

class ListParentStudents extends ListRecords
{
    protected static string $resource = ParentStudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
