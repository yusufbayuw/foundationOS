<?php

namespace Modules\Cafeteria\Filament\Resources\CafeteriaTenants\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Cafeteria\Filament\Resources\CafeteriaTenants\CafeteriaTenantResource;

class ViewCafeteriaTenant extends ViewRecord
{
    protected static string $resource = CafeteriaTenantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
