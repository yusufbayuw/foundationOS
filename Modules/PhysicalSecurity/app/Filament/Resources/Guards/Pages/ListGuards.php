<?php

namespace Modules\PhysicalSecurity\Filament\Resources\Guards\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\PhysicalSecurity\Filament\Resources\Guards\GuardResource;

class ListGuards extends ListRecords
{
    protected static string $resource = GuardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
