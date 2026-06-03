<?php

namespace Modules\Ai\Filament\Resources\AiPromptTemplates;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Ai\Filament\Resources\AiPromptTemplates\Pages\CreateAiPromptTemplate;
use Modules\Ai\Filament\Resources\AiPromptTemplates\Pages\EditAiPromptTemplate;
use Modules\Ai\Filament\Resources\AiPromptTemplates\Pages\ListAiPromptTemplates;
use Modules\Ai\Filament\Resources\AiPromptTemplates\Pages\ViewAiPromptTemplate;
use Modules\Ai\Filament\Resources\AiPromptTemplates\Schemas\AiPromptTemplateForm;
use Modules\Ai\Filament\Resources\AiPromptTemplates\Schemas\AiPromptTemplateInfolist;
use Modules\Ai\Filament\Resources\AiPromptTemplates\Tables\AiPromptTemplatesTable;
use Modules\Ai\Models\AiPromptTemplate;
use Modules\Core\Filament\Support\ModuleResource;

class AiPromptTemplateResource extends ModuleResource
{
    protected static ?string $model = AiPromptTemplate::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return AiPromptTemplateForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AiPromptTemplateInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AiPromptTemplatesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAiPromptTemplates::route('/'),
            'create' => CreateAiPromptTemplate::route('/create'),
            'view' => ViewAiPromptTemplate::route('/{record}'),
            'edit' => EditAiPromptTemplate::route('/{record}/edit'),
        ];
    }
}
