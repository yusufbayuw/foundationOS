<?php

namespace Modules\Cafeteria\Filament\Resources\CafeteriaTransactions\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Cafeteria\Filament\Resources\CafeteriaTransactions\CafeteriaTransactionResource;

class ViewCafeteriaTransaction extends ViewRecord
{
    protected static string $resource = CafeteriaTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
