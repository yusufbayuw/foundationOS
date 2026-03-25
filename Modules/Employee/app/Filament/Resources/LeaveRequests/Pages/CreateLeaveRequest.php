<?php

namespace Modules\Employee\Filament\Resources\LeaveRequests\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Employee\Filament\Resources\LeaveRequests\LeaveRequestResource;

class CreateLeaveRequest extends CreateRecord
{
    protected static string $resource = LeaveRequestResource::class;
}
