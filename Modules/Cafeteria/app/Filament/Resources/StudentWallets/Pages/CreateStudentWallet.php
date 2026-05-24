<?php

namespace Modules\Cafeteria\Filament\Resources\StudentWallets\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Cafeteria\Filament\Resources\StudentWallets\StudentWalletResource;

class CreateStudentWallet extends CreateRecord
{
    protected static string $resource = StudentWalletResource::class;
}
