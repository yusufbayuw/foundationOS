<?php

namespace Modules\Capacity\Filament\Resources\CapacityUtilizations\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Capacity\Filament\Resources\CapacityUtilizations\CapacityUtilizationResource;

class ViewCapacityUtilization extends ViewRecord
{
    protected static string $resource = CapacityUtilizationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
