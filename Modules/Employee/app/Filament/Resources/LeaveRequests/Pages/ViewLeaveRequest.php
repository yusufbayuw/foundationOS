<?php

namespace Modules\Employee\Filament\Resources\LeaveRequests\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Employee\Filament\Resources\LeaveRequests\LeaveRequestResource;

class ViewLeaveRequest extends ViewRecord
{
    protected static string $resource = LeaveRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
