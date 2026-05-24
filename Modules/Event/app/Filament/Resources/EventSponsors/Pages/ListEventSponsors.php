<?php

namespace Modules\Event\Filament\Resources\EventSponsors\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Event\Filament\Resources\EventSponsors\EventSponsorResource;

class ListEventSponsors extends ListRecords
{
    protected static string $resource = EventSponsorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
