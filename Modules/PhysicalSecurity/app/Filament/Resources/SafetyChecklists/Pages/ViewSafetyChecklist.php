<?php

namespace Modules\PhysicalSecurity\Filament\Resources\SafetyChecklists\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\PhysicalSecurity\Filament\Resources\SafetyChecklists\SafetyChecklistResource;

class ViewSafetyChecklist extends ViewRecord
{
    protected static string $resource = SafetyChecklistResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
