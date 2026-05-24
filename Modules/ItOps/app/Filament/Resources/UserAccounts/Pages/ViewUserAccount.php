<?php

namespace Modules\ItOps\Filament\Resources\UserAccounts\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\ItOps\Filament\Resources\UserAccounts\UserAccountResource;

class ViewUserAccount extends ViewRecord
{
    protected static string $resource = UserAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
