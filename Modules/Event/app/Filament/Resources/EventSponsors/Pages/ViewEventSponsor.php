<?php

namespace Modules\Event\Filament\Resources\EventSponsors\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Event\Filament\Resources\EventSponsors\EventSponsorResource;

class ViewEventSponsor extends ViewRecord
{
    protected static string $resource = EventSponsorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
