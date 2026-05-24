<?php

namespace Modules\Donation\Filament\Resources\Wakafs\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Donation\Filament\Resources\Wakafs\WakafResource;

class ViewWakaf extends ViewRecord
{
    protected static string $resource = WakafResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
