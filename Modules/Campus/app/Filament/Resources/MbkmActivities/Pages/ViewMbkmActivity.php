<?php

namespace Modules\Campus\Filament\Resources\MbkmActivities\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Campus\Filament\Resources\MbkmActivities\MbkmActivityResource;

class ViewMbkmActivity extends ViewRecord
{
    protected static string $resource = MbkmActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
