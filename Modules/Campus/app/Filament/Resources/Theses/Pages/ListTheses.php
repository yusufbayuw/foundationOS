<?php

namespace Modules\Campus\Filament\Resources\Theses\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Campus\Filament\Resources\Theses\ThesisResource;

class ListTheses extends ListRecords
{
    protected static string $resource = ThesisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
