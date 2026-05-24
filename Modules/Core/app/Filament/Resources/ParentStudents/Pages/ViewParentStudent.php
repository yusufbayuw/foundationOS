<?php

namespace Modules\Core\Filament\Resources\ParentStudents\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Core\Filament\Resources\ParentStudents\ParentStudentResource;

class ViewParentStudent extends ViewRecord
{
    protected static string $resource = ParentStudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
