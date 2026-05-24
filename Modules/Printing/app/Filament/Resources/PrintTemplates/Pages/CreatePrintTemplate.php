<?php

namespace Modules\Printing\Filament\Resources\PrintTemplates\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Printing\Filament\Resources\PrintTemplates\PrintTemplateResource;

class CreatePrintTemplate extends CreateRecord
{
    protected static string $resource = PrintTemplateResource::class;
}
