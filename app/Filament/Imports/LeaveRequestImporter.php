<?php

namespace App\Filament\Imports;

use Modules\Employee\Models\LeaveRequest;

class LeaveRequestImporter extends BaseModelImporter
{
    protected static ?string $model = LeaveRequest::class;
}
