<?php

namespace Modules\Donation\Filament\Resources\CampaignUpdates\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Donation\Filament\Resources\CampaignUpdates\CampaignUpdateResource;

class EditCampaignUpdate extends EditRecord
{
    protected static string $resource = CampaignUpdateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
