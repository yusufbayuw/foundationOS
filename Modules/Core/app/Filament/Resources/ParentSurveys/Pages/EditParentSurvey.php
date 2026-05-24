<?php

namespace Modules\Core\Filament\Resources\ParentSurveys\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Core\Filament\Resources\ParentSurveys\ParentSurveyResource;

class EditParentSurvey extends EditRecord
{
    protected static string $resource = ParentSurveyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
