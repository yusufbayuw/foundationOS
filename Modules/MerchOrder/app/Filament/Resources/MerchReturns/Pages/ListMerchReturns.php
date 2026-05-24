<?php

namespace Modules\MerchOrder\Filament\Resources\MerchReturns\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\MerchOrder\Filament\Resources\MerchReturns\MerchReturnResource;

class ListMerchReturns extends ListRecords
{
    protected static string $resource = MerchReturnResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
