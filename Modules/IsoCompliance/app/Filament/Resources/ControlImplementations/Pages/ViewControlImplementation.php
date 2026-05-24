<?php

namespace Modules\IsoCompliance\Filament\Resources\ControlImplementations\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\IsoCompliance\Filament\Resources\ControlImplementations\ControlImplementationResource;

class ViewControlImplementation extends ViewRecord
{
    protected static string $resource = ControlImplementationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
