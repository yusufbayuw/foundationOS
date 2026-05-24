<?php

namespace Modules\PhysicalSecurity\Filament\Resources\Guards\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\PhysicalSecurity\Filament\Resources\Guards\GuardResource;

class CreateGuard extends CreateRecord
{
    protected static string $resource = GuardResource::class;
}
