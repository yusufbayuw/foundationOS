<?php

namespace Modules\Printing\Filament\Resources\Royalties\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Printing\Filament\Resources\Royalties\RoyaltyResource;

class EditRoyalty extends EditRecord
{
    protected static string $resource = RoyaltyResource::class;

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
