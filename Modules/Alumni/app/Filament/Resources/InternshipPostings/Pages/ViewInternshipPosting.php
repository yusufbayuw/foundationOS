<?php

namespace Modules\Alumni\Filament\Resources\InternshipPostings\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Alumni\Filament\Resources\InternshipPostings\InternshipPostingResource;

class ViewInternshipPosting extends ViewRecord
{
    protected static string $resource = InternshipPostingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
