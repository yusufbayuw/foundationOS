<?php

namespace Modules\School\Filament\Resources\Curricula\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\School\Filament\Resources\Curricula\CurriculumResource;

class CreateCurriculum extends CreateRecord
{
    protected static string $resource = CurriculumResource::class;
}
