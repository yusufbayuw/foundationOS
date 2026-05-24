<?php

namespace Modules\Property\Filament\Resources\CommercialTenants\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Property\Filament\Resources\CommercialTenants\CommercialTenantResource;

class ViewCommercialTenant extends ViewRecord
{
    protected static string $resource = CommercialTenantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
