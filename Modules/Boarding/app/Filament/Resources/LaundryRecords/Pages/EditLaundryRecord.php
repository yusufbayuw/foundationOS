<?php

namespace Modules\Boarding\Filament\Resources\LaundryRecords\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Boarding\Filament\Resources\LaundryRecords\LaundryRecordResource;

class EditLaundryRecord extends EditRecord
{
    protected static string $resource = LaundryRecordResource::class;

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
