<?php

namespace Modules\Enrollment\Filament\Resources\LeadActivities\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Enrollment\Filament\Resources\LeadActivities\LeadActivityResource;

class ListLeadActivities extends ListRecords
{
    protected static string $resource = LeadActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
