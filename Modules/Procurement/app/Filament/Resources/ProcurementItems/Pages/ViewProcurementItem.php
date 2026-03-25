<?php

namespace Modules\Procurement\Filament\Resources\ProcurementItems\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Procurement\Filament\Resources\ProcurementItems\ProcurementItemResource;

class ViewProcurementItem extends ViewRecord
{
    protected static string $resource = ProcurementItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
