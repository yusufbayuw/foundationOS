<?php

namespace Modules\ItOps\Filament\Resources\UserAccounts\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\ItOps\Filament\Resources\UserAccounts\UserAccountResource;

class CreateUserAccount extends CreateRecord
{
    protected static string $resource = UserAccountResource::class;
}
