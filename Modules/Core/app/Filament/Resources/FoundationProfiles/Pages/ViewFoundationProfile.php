<?php

namespace Modules\Core\Filament\Resources\FoundationProfiles\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Core\Filament\Resources\FoundationProfiles\FoundationProfileResource;

class ViewFoundationProfile extends ViewRecord
{
    protected static string $resource = FoundationProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
