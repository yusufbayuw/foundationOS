<?php

namespace Modules\Core\Filament\Resources\FoundationProfiles\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Core\Filament\Resources\FoundationProfiles\FoundationProfileResource;

class EditFoundationProfile extends EditRecord
{
    protected static string $resource = FoundationProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
