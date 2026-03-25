<?php

namespace Modules\Core\Filament\Resources\Modules\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Core\Filament\Resources\Modules\ModuleResource;

class CreateModule extends CreateRecord
{
    protected static string $resource = ModuleResource::class;
}
