<?php

namespace Modules\Cms\Filament\Resources\HomepageSections\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Cms\Filament\Resources\HomepageSections\HomepageSectionResource;

class EditHomepageSection extends EditRecord
{
    protected static string $resource = HomepageSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
