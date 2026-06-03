<?php

namespace App\Filament\Imports;

use Modules\Ai\Models\AiPromptTemplate;

class AiPromptTemplateImporter extends BaseModelImporter
{
    protected static ?string $model = AiPromptTemplate::class;
}
