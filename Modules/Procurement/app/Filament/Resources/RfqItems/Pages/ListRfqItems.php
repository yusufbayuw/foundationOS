<?php

namespace Modules\Procurement\Filament\Resources\RfqItems\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Procurement\Filament\Resources\RfqItems\RfqItemResource;

class ListRfqItems extends ListRecords
{
    protected static string $resource = RfqItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
