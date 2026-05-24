<?php

namespace Modules\Event\Filament\Resources\EventCertificates\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Event\Filament\Resources\EventCertificates\EventCertificateResource;

class ViewEventCertificate extends ViewRecord
{
    protected static string $resource = EventCertificateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
