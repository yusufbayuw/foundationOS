<?php

namespace Modules\Alumni\Filament\Resources\AlumnusEducation\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Alumni\Filament\Resources\AlumnusEducation\AlumnusEducationResource;

class EditAlumnusEducation extends EditRecord
{
    protected static string $resource = AlumnusEducationResource::class;

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
