<?php

namespace Modules\Consulting\Filament\Resources\ConsultingClients\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Consulting\Filament\Resources\ConsultingClients\ConsultingClientResource;

class CreateConsultingClient extends CreateRecord
{
    protected static string $resource = ConsultingClientResource::class;
}
