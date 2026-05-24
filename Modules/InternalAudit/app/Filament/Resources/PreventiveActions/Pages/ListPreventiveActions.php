<?php

namespace Modules\InternalAudit\Filament\Resources\PreventiveActions\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\InternalAudit\Filament\Resources\PreventiveActions\PreventiveActionResource;

class ListPreventiveActions extends ListRecords
{
    protected static string $resource = PreventiveActionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
