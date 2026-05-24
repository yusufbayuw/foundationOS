<?php

namespace Modules\Alumni\Filament\Resources\JobPostings\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Alumni\Filament\Resources\JobPostings\JobPostingResource;

class ViewJobPosting extends ViewRecord
{
    protected static string $resource = JobPostingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
