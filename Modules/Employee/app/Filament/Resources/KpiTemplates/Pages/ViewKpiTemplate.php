<?php

namespace Modules\Employee\Filament\Resources\KpiTemplates\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Employee\Filament\Resources\KpiTemplates\KpiTemplateResource;

class ViewKpiTemplate extends ViewRecord
{
    protected static string $resource = KpiTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
