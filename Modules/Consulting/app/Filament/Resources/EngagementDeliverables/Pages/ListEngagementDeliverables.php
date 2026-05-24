<?php

namespace Modules\Consulting\Filament\Resources\EngagementDeliverables\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Consulting\Filament\Resources\EngagementDeliverables\EngagementDeliverableResource;

class ListEngagementDeliverables extends ListRecords
{
    protected static string $resource = EngagementDeliverableResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
