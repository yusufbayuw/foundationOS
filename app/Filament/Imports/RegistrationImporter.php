<?php

namespace App\Filament\Imports;

use Modules\Enrollment\Models\Registration;

class RegistrationImporter extends BaseModelImporter
{
    protected static ?string $model = Registration::class;
}
