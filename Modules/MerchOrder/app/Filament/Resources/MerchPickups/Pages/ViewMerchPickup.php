<?php

namespace Modules\MerchOrder\Filament\Resources\MerchPickups\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\MerchOrder\Filament\Resources\MerchPickups\MerchPickupResource;

class ViewMerchPickup extends ViewRecord
{
    protected static string $resource = MerchPickupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
