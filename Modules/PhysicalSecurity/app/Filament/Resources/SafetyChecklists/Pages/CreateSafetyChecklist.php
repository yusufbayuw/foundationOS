<?php

namespace Modules\PhysicalSecurity\Filament\Resources\SafetyChecklists\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\PhysicalSecurity\Filament\Resources\SafetyChecklists\SafetyChecklistResource;

class CreateSafetyChecklist extends CreateRecord
{
    protected static string $resource = SafetyChecklistResource::class;
}
