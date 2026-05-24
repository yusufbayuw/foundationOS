<?php

namespace Modules\IsoCompliance\Filament\Resources\IsoPolicies\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\IsoCompliance\Filament\Resources\IsoPolicies\IsoPolicyResource;

class ViewIsoPolicy extends ViewRecord
{
    protected static string $resource = IsoPolicyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
