<?php

namespace Modules\MerchOrder\Filament\Resources\MerchReturns\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\MerchOrder\Filament\Resources\MerchReturns\MerchReturnResource;

class ViewMerchReturn extends ViewRecord
{
    protected static string $resource = MerchReturnResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
