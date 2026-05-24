<?php

namespace Modules\Event\Filament\Resources\EventCertificates\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Event\Filament\Resources\EventCertificates\EventCertificateResource;

class CreateEventCertificate extends CreateRecord
{
    protected static string $resource = EventCertificateResource::class;
}
