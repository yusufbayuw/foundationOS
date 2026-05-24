<?php

namespace Modules\PhysicalSecurity\Filament\Resources\VisitorLogs\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\PhysicalSecurity\Filament\Resources\VisitorLogs\VisitorLogResource;

class ViewVisitorLog extends ViewRecord
{
    protected static string $resource = VisitorLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
