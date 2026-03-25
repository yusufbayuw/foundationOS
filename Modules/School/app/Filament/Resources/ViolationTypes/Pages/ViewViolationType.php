<?php

namespace Modules\School\Filament\Resources\ViolationTypes\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\School\Filament\Resources\ViolationTypes\ViolationTypeResource;

class ViewViolationType extends ViewRecord
{
    protected static string $resource = ViolationTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
