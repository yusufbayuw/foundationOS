<?php

namespace Modules\Library\Filament\Resources\Fines\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Library\Filament\Resources\Fines\FineResource;

class ViewFine extends ViewRecord
{
    protected static string $resource = FineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
