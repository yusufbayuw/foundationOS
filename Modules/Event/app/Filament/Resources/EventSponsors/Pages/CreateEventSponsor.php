<?php

namespace Modules\Event\Filament\Resources\EventSponsors\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Event\Filament\Resources\EventSponsors\EventSponsorResource;

class CreateEventSponsor extends CreateRecord
{
    protected static string $resource = EventSponsorResource::class;
}
