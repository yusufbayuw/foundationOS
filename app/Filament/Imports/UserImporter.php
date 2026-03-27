<?php

namespace App\Filament\Imports;

use Modules\Core\Models\User;

class UserImporter extends BaseModelImporter
{
    protected static ?string $model = User::class;
}
