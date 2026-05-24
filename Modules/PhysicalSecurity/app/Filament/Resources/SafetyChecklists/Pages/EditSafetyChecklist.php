<?php

namespace Modules\PhysicalSecurity\Filament\Resources\SafetyChecklists\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\PhysicalSecurity\Filament\Resources\SafetyChecklists\SafetyChecklistResource;

class EditSafetyChecklist extends EditRecord
{
    protected static string $resource = SafetyChecklistResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
