<?php

namespace Modules\Transport\Filament\Resources\StudentShuttleSubscriptions\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Transport\Filament\Resources\StudentShuttleSubscriptions\StudentShuttleSubscriptionResource;

class ViewStudentShuttleSubscription extends ViewRecord
{
    protected static string $resource = StudentShuttleSubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
