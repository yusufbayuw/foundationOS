<?php

namespace Modules\Core\Filament\Resources\ParentTeacherMessages\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Core\Filament\Resources\ParentTeacherMessages\ParentTeacherMessageResource;

class EditParentTeacherMessage extends EditRecord
{
    protected static string $resource = ParentTeacherMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
