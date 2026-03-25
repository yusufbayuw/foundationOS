<?php

namespace Modules\Employee\Filament\Resources\KpiTemplates\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Employee\Filament\Resources\KpiTemplates\KpiTemplateResource;

class ListKpiTemplates extends ListRecords
{
    protected static string $resource = KpiTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
