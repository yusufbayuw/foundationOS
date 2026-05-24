<?php

namespace Modules\Cms\Filament\Resources\CmsMedia\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Cms\Filament\Resources\CmsMedia\CmsMediaResource;

class EditCmsMedia extends EditRecord
{
    protected static string $resource = CmsMediaResource::class;

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
