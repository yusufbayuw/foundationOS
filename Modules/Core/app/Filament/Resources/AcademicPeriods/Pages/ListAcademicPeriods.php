<?php

namespace Modules\Core\Filament\Resources\AcademicPeriods\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Core\Filament\Resources\AcademicPeriods\AcademicPeriodResource;

class ListAcademicPeriods extends ListRecords
{
    protected static string $resource = AcademicPeriodResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
