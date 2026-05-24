<?php

namespace Modules\Alumni\Filament\Resources\AlumnusEmployments\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Alumni\Filament\Resources\AlumnusEmployments\AlumnusEmploymentResource;

class EditAlumnusEmployment extends EditRecord
{
    protected static string $resource = AlumnusEmploymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
