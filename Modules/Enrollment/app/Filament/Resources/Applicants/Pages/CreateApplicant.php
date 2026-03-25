<?php

namespace Modules\Enrollment\Filament\Resources\Applicants\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Enrollment\Filament\Resources\Applicants\ApplicantResource;

class CreateApplicant extends CreateRecord
{
    protected static string $resource = ApplicantResource::class;
}
