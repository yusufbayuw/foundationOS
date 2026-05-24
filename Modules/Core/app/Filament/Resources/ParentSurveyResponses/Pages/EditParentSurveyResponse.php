<?php

namespace Modules\Core\Filament\Resources\ParentSurveyResponses\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Core\Filament\Resources\ParentSurveyResponses\ParentSurveyResponseResource;

class EditParentSurveyResponse extends EditRecord
{
    protected static string $resource = ParentSurveyResponseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
