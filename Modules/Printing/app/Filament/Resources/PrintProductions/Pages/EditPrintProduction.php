<?php

namespace Modules\Printing\Filament\Resources\PrintProductions\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Printing\Filament\Resources\PrintProductions\PrintProductionResource;

class EditPrintProduction extends EditRecord
{
    protected static string $resource = PrintProductionResource::class;

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
