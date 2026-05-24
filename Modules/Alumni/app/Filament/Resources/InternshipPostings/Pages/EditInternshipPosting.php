<?php

namespace Modules\Alumni\Filament\Resources\InternshipPostings\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Alumni\Filament\Resources\InternshipPostings\InternshipPostingResource;

class EditInternshipPosting extends EditRecord
{
    protected static string $resource = InternshipPostingResource::class;

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
