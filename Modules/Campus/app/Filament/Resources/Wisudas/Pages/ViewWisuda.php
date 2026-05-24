<?php

namespace Modules\Campus\Filament\Resources\Wisudas\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Campus\Filament\Resources\Wisudas\WisudaResource;

class ViewWisuda extends ViewRecord
{
    protected static string $resource = WisudaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
