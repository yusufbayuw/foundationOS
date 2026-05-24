<?php

namespace Modules\IsoCompliance\Filament\Resources\ControlImplementations\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\IsoCompliance\Filament\Resources\ControlImplementations\ControlImplementationResource;

class EditControlImplementation extends EditRecord
{
    protected static string $resource = ControlImplementationResource::class;

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
