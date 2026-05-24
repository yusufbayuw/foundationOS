<?php

namespace Modules\Transport\Filament\Resources\StudentShuttleSubscriptions\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Transport\Filament\Resources\StudentShuttleSubscriptions\StudentShuttleSubscriptionResource;

class ListStudentShuttleSubscriptions extends ListRecords
{
    protected static string $resource = StudentShuttleSubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
