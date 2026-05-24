<?php

namespace Modules\InternalAudit\Filament\Resources\CorrectiveActions\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\InternalAudit\Filament\Resources\CorrectiveActions\CorrectiveActionResource;

class ListCorrectiveActions extends ListRecords
{
    protected static string $resource = CorrectiveActionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
