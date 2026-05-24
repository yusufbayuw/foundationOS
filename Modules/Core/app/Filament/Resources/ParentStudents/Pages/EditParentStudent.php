<?php

namespace Modules\Core\Filament\Resources\ParentStudents\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Core\Filament\Resources\ParentStudents\ParentStudentResource;

class EditParentStudent extends EditRecord
{
    protected static string $resource = ParentStudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
