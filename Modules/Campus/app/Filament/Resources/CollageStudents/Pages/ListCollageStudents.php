<?php

namespace Modules\Campus\Filament\Resources\CollageStudents\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Campus\Filament\Resources\CollageStudents\CollageStudentResource;

class ListCollageStudents extends ListRecords
{
    protected static string $resource = CollageStudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
