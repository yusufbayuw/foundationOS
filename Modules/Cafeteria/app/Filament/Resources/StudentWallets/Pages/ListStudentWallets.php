<?php

namespace Modules\Cafeteria\Filament\Resources\StudentWallets\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Cafeteria\Filament\Resources\StudentWallets\StudentWalletResource;

class ListStudentWallets extends ListRecords
{
    protected static string $resource = StudentWalletResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
