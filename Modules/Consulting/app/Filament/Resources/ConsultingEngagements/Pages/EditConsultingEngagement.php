<?php

namespace Modules\Consulting\Filament\Resources\ConsultingEngagements\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Consulting\Filament\Resources\ConsultingEngagements\ConsultingEngagementResource;

class EditConsultingEngagement extends EditRecord
{
    protected static string $resource = ConsultingEngagementResource::class;

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
