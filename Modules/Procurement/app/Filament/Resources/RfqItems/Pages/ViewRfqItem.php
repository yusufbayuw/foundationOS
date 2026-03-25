<?php

namespace Modules\Procurement\Filament\Resources\RfqItems\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Procurement\Filament\Resources\RfqItems\RfqItemResource;

class ViewRfqItem extends ViewRecord
{
    protected static string $resource = RfqItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
