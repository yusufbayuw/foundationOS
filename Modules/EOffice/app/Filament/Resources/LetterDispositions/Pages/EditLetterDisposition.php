<?php

namespace Modules\EOffice\Filament\Resources\LetterDispositions\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\EOffice\Filament\Resources\LetterDispositions\LetterDispositionResource;

class EditLetterDisposition extends EditRecord
{
    protected static string $resource = LetterDispositionResource::class;

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
