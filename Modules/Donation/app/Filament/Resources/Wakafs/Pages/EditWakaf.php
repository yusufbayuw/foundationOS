<?php

namespace Modules\Donation\Filament\Resources\Wakafs\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Donation\Filament\Resources\Wakafs\WakafResource;

class EditWakaf extends EditRecord
{
    protected static string $resource = WakafResource::class;

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
