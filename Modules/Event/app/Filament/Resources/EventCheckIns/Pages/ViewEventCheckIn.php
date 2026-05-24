<?php

namespace Modules\Event\Filament\Resources\EventCheckIns\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Event\Filament\Resources\EventCheckIns\EventCheckInResource;

class ViewEventCheckIn extends ViewRecord
{
    protected static string $resource = EventCheckInResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
