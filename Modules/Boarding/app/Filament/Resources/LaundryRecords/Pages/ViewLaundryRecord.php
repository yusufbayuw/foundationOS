<?php

namespace Modules\Boarding\Filament\Resources\LaundryRecords\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Boarding\Filament\Resources\LaundryRecords\LaundryRecordResource;

class ViewLaundryRecord extends ViewRecord
{
    protected static string $resource = LaundryRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
