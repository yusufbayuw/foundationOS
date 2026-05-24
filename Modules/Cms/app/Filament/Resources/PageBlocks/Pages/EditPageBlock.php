<?php

namespace Modules\Cms\Filament\Resources\PageBlocks\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Cms\Filament\Resources\PageBlocks\PageBlockResource;

class EditPageBlock extends EditRecord
{
    protected static string $resource = PageBlockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
