<?php

namespace Modules\Event\Filament\Resources\EventCertificates\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Event\Filament\Resources\EventCertificates\EventCertificateResource;

class ListEventCertificates extends ListRecords
{
    protected static string $resource = EventCertificateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
