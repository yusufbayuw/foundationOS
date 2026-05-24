<?php

namespace Modules\Sales\Filament\Resources\CooperativeSavings\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Sales\Filament\Resources\CooperativeSavings\CooperativeSavingResource;

class ListCooperativeSavings extends ListRecords
{
    protected static string $resource = CooperativeSavingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
