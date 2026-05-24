<?php

namespace Modules\PhysicalSecurity\Filament\Resources\PatrolLogs\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\PhysicalSecurity\Filament\Resources\PatrolLogs\PatrolLogResource;

class ListPatrolLogs extends ListRecords
{
    protected static string $resource = PatrolLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
