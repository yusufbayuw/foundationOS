<?php

namespace Modules\Core\Filament\Resources\FoundationProfiles\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Core\Filament\Resources\FoundationProfiles\FoundationProfileResource;

class ListFoundationProfiles extends ListRecords
{
    protected static string $resource = FoundationProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
