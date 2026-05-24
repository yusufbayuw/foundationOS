<?php

namespace Modules\Donation\Filament\Resources\Endowments\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Donation\Filament\Resources\Endowments\EndowmentResource;

class ViewEndowment extends ViewRecord
{
    protected static string $resource = EndowmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
