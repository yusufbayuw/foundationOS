<?php

namespace Modules\PhysicalSecurity\Filament\Resources\Guards\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\PhysicalSecurity\Filament\Resources\Guards\GuardResource;

class ViewGuard extends ViewRecord
{
    protected static string $resource = GuardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
