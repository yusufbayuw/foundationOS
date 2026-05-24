<?php

namespace Modules\School\Filament\Resources\ExtracurricularEnrollments\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\School\Filament\Resources\ExtracurricularEnrollments\ExtracurricularEnrollmentResource;

class CreateExtracurricularEnrollment extends CreateRecord
{
    protected static string $resource = ExtracurricularEnrollmentResource::class;
}
