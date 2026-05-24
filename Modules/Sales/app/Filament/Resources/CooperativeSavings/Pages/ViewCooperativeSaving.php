<?php

namespace Modules\Sales\Filament\Resources\CooperativeSavings\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Sales\Filament\Resources\CooperativeSavings\CooperativeSavingResource;

class ViewCooperativeSaving extends ViewRecord
{
    protected static string $resource = CooperativeSavingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
