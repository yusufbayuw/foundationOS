<?php

namespace Modules\Counseling\Filament\Resources\CaseFollowUps\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Counseling\Filament\Resources\CaseFollowUps\CaseFollowUpResource;

class ViewCaseFollowUp extends ViewRecord
{
    protected static string $resource = CaseFollowUpResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
