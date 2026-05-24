<?php

namespace Modules\Printing\Filament\Resources\PrintOrderItems\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Printing\Filament\Resources\PrintOrderItems\PrintOrderItemResource;

class ViewPrintOrderItem extends ViewRecord
{
    protected static string $resource = PrintOrderItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
