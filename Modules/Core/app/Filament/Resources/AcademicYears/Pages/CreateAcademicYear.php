<?php

namespace Modules\Core\Filament\Resources\AcademicYears\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Core\Filament\Resources\AcademicYears\AcademicYearResource;

class CreateAcademicYear extends CreateRecord
{
    protected static string $resource = AcademicYearResource::class;
}
