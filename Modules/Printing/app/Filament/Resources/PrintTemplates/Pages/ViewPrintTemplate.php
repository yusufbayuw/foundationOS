<?php

namespace Modules\Printing\Filament\Resources\PrintTemplates\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Printing\Filament\Resources\PrintTemplates\PrintTemplateResource;

class ViewPrintTemplate extends ViewRecord
{
    protected static string $resource = PrintTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
