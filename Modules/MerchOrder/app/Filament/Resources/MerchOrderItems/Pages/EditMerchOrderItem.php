<?php

namespace Modules\MerchOrder\Filament\Resources\MerchOrderItems\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\MerchOrder\Filament\Resources\MerchOrderItems\MerchOrderItemResource;

class EditMerchOrderItem extends EditRecord
{
    protected static string $resource = MerchOrderItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
