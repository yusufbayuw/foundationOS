<?php

namespace Modules\Enrollment\Filament\Resources\LeadActivities\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Enrollment\Filament\Resources\LeadActivities\LeadActivityResource;

class ViewLeadActivity extends ViewRecord
{
    protected static string $resource = LeadActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
