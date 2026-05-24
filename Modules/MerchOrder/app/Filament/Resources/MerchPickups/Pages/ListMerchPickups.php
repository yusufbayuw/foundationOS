<?php

namespace Modules\MerchOrder\Filament\Resources\MerchPickups\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\MerchOrder\Filament\Resources\MerchPickups\MerchPickupResource;

class ListMerchPickups extends ListRecords
{
    protected static string $resource = MerchPickupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
