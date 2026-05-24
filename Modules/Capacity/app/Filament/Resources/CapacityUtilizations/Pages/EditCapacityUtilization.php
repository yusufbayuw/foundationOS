<?php

namespace Modules\Capacity\Filament\Resources\CapacityUtilizations\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Capacity\Filament\Resources\CapacityUtilizations\CapacityUtilizationResource;

class EditCapacityUtilization extends EditRecord
{
    protected static string $resource = CapacityUtilizationResource::class;

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
