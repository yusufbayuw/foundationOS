<?php

namespace Modules\Cafeteria\Filament\Resources\CafeteriaTenants\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Cafeteria\Filament\Resources\CafeteriaTenants\CafeteriaTenantResource;

class CreateCafeteriaTenant extends CreateRecord
{
    protected static string $resource = CafeteriaTenantResource::class;
}
