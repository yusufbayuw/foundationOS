<?php

namespace Modules\Core\Filament\Resources\Broadcasts\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Core\Filament\Resources\Broadcasts\BroadcastResource;

class ViewBroadcast extends ViewRecord
{
    protected static string $resource = BroadcastResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
