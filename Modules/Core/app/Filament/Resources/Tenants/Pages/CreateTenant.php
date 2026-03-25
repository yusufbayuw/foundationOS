<?php

namespace Modules\Core\Filament\Resources\Tenants\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Core\Filament\Resources\Tenants\TenantResource;

class CreateTenant extends CreateRecord
{
    protected static string $resource = TenantResource::class;
}
