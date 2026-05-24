<?php

namespace Modules\Property\Filament\Resources\CommercialTenants\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Property\Filament\Resources\CommercialTenants\CommercialTenantResource;

class ListCommercialTenants extends ListRecords
{
    protected static string $resource = CommercialTenantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
