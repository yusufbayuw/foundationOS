<?php

namespace Modules\Clinic\Filament\Resources\HealthRecords\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Clinic\Filament\Resources\HealthRecords\HealthRecordResource;

class ViewHealthRecord extends ViewRecord
{
    protected static string $resource = HealthRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
