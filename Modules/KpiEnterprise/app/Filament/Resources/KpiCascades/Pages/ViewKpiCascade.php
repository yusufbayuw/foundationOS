<?php

namespace Modules\KpiEnterprise\Filament\Resources\KpiCascades\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\KpiEnterprise\Filament\Resources\KpiCascades\KpiCascadeResource;

class ViewKpiCascade extends ViewRecord
{
    protected static string $resource = KpiCascadeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
