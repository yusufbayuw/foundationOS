<?php

namespace Modules\Printing\Filament\Resources\Royalties\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Printing\Filament\Resources\Royalties\RoyaltyResource;

class ViewRoyalty extends ViewRecord
{
    protected static string $resource = RoyaltyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
