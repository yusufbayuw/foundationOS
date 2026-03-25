<?php

namespace Modules\Enrollment\Filament\Resources\Registrations\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Enrollment\Filament\Resources\Registrations\RegistrationResource;

class CreateRegistration extends CreateRecord
{
    protected static string $resource = RegistrationResource::class;
}
