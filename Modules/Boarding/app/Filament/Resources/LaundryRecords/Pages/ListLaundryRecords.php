<?php

namespace Modules\Boarding\Filament\Resources\LaundryRecords\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Boarding\Filament\Resources\LaundryRecords\LaundryRecordResource;

class ListLaundryRecords extends ListRecords
{
    protected static string $resource = LaundryRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
