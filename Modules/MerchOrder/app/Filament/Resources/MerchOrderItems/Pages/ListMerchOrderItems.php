<?php

namespace Modules\MerchOrder\Filament\Resources\MerchOrderItems\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\MerchOrder\Filament\Resources\MerchOrderItems\MerchOrderItemResource;

class ListMerchOrderItems extends ListRecords
{
    protected static string $resource = MerchOrderItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
