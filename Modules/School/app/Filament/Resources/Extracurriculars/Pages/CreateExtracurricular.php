<?php

namespace Modules\School\Filament\Resources\Extracurriculars\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\School\Filament\Resources\Extracurriculars\ExtracurricularResource;

class CreateExtracurricular extends CreateRecord
{
    protected static string $resource = ExtracurricularResource::class;
}
