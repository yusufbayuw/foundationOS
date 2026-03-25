<?php

namespace Modules\Campus\Filament\Resources\CollageStudents\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Campus\Filament\Resources\CollageStudents\CollageStudentResource;

class ViewCollageStudent extends ViewRecord
{
    protected static string $resource = CollageStudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
