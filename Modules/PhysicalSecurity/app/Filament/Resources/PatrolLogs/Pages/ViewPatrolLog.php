<?php

namespace Modules\PhysicalSecurity\Filament\Resources\PatrolLogs\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\PhysicalSecurity\Filament\Resources\PatrolLogs\PatrolLogResource;

class ViewPatrolLog extends ViewRecord
{
    protected static string $resource = PatrolLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
