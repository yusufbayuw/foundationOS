<?php

namespace Modules\Enrollment\Filament\Resources\LeadSources\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Enrollment\Filament\Resources\LeadSources\LeadSourceResource;

class ViewLeadSource extends ViewRecord
{
    protected static string $resource = LeadSourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
