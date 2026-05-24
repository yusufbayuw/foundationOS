<?php

namespace Modules\Facility\Filament\Resources\Floors\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Facility\Filament\Resources\Floors\FloorResource;

class ViewFloor extends ViewRecord
{
    protected static string $resource = FloorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
