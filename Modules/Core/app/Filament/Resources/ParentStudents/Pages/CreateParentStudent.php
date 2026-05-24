<?php

namespace Modules\Core\Filament\Resources\ParentStudents\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Core\Filament\Resources\ParentStudents\ParentStudentResource;

class CreateParentStudent extends CreateRecord
{
    protected static string $resource = ParentStudentResource::class;
}
