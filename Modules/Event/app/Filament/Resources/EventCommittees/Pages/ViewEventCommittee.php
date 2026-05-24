<?php

namespace Modules\Event\Filament\Resources\EventCommittees\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Event\Filament\Resources\EventCommittees\EventCommitteeResource;

class ViewEventCommittee extends ViewRecord
{
    protected static string $resource = EventCommitteeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
