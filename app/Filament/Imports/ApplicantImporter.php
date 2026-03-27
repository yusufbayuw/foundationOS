<?php

namespace App\Filament\Imports;

use Modules\Enrollment\Models\Applicant;

class ApplicantImporter extends BaseModelImporter
{
    protected static ?string $model = Applicant::class;
}
