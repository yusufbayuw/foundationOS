<?php

namespace Modules\Cafeteria\Filament\Resources\StudentWallets\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Cafeteria\Filament\Resources\StudentWallets\StudentWalletResource;

class EditStudentWallet extends EditRecord
{
    protected static string $resource = StudentWalletResource::class;

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
