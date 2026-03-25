<?php

namespace Modules\School\Filament\Resources\ClassStudents\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\School\Filament\Resources\ClassStudents\ClassStudentResource;

class ViewClassStudent extends ViewRecord
{
    protected static string $resource = ClassStudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
