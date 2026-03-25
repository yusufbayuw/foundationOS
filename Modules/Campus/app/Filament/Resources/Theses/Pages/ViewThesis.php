<?php

namespace Modules\Campus\Filament\Resources\Theses\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Campus\Filament\Resources\Theses\ThesisResource;

class ViewThesis extends ViewRecord
{
    protected static string $resource = ThesisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
