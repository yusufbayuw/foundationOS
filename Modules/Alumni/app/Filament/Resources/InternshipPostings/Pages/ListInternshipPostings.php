<?php

namespace Modules\Alumni\Filament\Resources\InternshipPostings\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Alumni\Filament\Resources\InternshipPostings\InternshipPostingResource;

class ListInternshipPostings extends ListRecords
{
    protected static string $resource = InternshipPostingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
