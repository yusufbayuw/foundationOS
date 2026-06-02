<?php

namespace App\Filament\Imports;

use Modules\Cms\Models\Site;

class CmsSiteImporter extends BaseModelImporter
{
    protected static ?string $model = Site::class;
}
