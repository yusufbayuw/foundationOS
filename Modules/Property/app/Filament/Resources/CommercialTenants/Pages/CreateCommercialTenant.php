<?php

namespace Modules\Property\Filament\Resources\CommercialTenants\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Property\Filament\Resources\CommercialTenants\CommercialTenantResource;

class CreateCommercialTenant extends CreateRecord
{
    protected static string $resource = CommercialTenantResource::class;
}
