<?php

namespace Modules\Consulting\Filament\Resources\EngagementDeliverables\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Consulting\Filament\Resources\EngagementDeliverables\EngagementDeliverableResource;

class ViewEngagementDeliverable extends ViewRecord
{
    protected static string $resource = EngagementDeliverableResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
