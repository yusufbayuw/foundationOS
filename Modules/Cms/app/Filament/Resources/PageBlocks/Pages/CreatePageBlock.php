<?php

namespace Modules\Cms\Filament\Resources\PageBlocks\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Cms\Filament\Resources\PageBlocks\PageBlockResource;

class CreatePageBlock extends CreateRecord
{
    protected static string $resource = PageBlockResource::class;
}
