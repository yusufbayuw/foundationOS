<?php

namespace Modules\PhysicalSecurity\Filament\Resources\SafetyChecklists\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\PhysicalSecurity\Filament\Resources\SafetyChecklists\SafetyChecklistResource;

class ListSafetyChecklists extends ListRecords
{
    protected static string $resource = SafetyChecklistResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
