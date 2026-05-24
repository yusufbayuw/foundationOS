<?php

namespace Modules\IsoCompliance\Filament\Resources\IsoPolicies\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\IsoCompliance\Filament\Resources\IsoPolicies\IsoPolicyResource;

class ListIsoPolicies extends ListRecords
{
    protected static string $resource = IsoPolicyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
