<?php

namespace Modules\Core\Filament\Resources\ParentTeacherMessages\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Core\Filament\Resources\ParentTeacherMessages\ParentTeacherMessageResource;

class ViewParentTeacherMessage extends ViewRecord
{
    protected static string $resource = ParentTeacherMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
