<?php

namespace Modules\Printing\Filament\Resources\PrintOrders\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Printing\Filament\Resources\PrintOrders\PrintOrderResource;

class ViewPrintOrder extends ViewRecord
{
    protected static string $resource = PrintOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
