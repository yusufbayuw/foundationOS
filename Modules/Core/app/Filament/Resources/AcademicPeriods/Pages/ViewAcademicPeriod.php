<?php

namespace Modules\Core\Filament\Resources\AcademicPeriods\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Core\Filament\Resources\AcademicPeriods\AcademicPeriodResource;

class ViewAcademicPeriod extends ViewRecord
{
    protected static string $resource = AcademicPeriodResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
