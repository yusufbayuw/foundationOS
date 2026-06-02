<?php

namespace App\Filament\Imports;

use Modules\Training\Models\Instructor;

class InstructorImporter extends BaseModelImporter
{
    protected static ?string $model = Instructor::class;
}
