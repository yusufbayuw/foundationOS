<?php

namespace Modules\Campus\Filament\Resources\Yudisia\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Campus\Filament\Resources\Yudisia\YudisiumResource;

class ListYudisia extends ListRecords
{
    protected static string $resource = YudisiumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
