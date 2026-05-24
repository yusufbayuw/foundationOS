<?php

namespace Modules\Cafeteria\Filament\Resources\StudentWallets\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Cafeteria\Filament\Resources\StudentWallets\StudentWalletResource;

class ViewStudentWallet extends ViewRecord
{
    protected static string $resource = StudentWalletResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
