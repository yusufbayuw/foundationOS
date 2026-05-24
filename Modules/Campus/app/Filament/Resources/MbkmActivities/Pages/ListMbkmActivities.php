<?php

namespace Modules\Campus\Filament\Resources\MbkmActivities\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Campus\Filament\Resources\MbkmActivities\MbkmActivityResource;

class ListMbkmActivities extends ListRecords
{
    protected static string $resource = MbkmActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
