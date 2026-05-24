<?php

namespace Modules\Campus\Filament\Resources\Yudisia\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Campus\Filament\Resources\Yudisia\YudisiumResource;

class ViewYudisium extends ViewRecord
{
    protected static string $resource = YudisiumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
