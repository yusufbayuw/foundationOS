<?php

namespace Modules\MerchOrder\Filament\Resources\MerchOrderItems\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\MerchOrder\Filament\Resources\MerchOrderItems\MerchOrderItemResource;

class ViewMerchOrderItem extends ViewRecord
{
    protected static string $resource = MerchOrderItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
