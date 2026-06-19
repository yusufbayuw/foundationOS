<?php

namespace Modules\Employee\Filament\Resources\KpiTemplates;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Modules\Employee\Filament\Resources\KpiTemplates\Pages\CreateKpiTemplate;
use Modules\Employee\Filament\Resources\KpiTemplates\Pages\EditKpiTemplate;
use Modules\Employee\Filament\Resources\KpiTemplates\Pages\ListKpiTemplates;
use Modules\Employee\Filament\Resources\KpiTemplates\Pages\ViewKpiTemplate;
use Modules\Employee\Filament\Resources\KpiTemplates\Schemas\KpiTemplateForm;
use Modules\Employee\Filament\Resources\KpiTemplates\Schemas\KpiTemplateInfolist;
use Modules\Employee\Filament\Resources\KpiTemplates\Tables\KpiTemplatesTable;
use Modules\Employee\Models\KpiTemplate;

class KpiTemplateResource extends LocalizedResource
{
    protected static ?string $model = KpiTemplate::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return KpiTemplateForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return KpiTemplateInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KpiTemplatesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListKpiTemplates::route('/'),
            'create' => CreateKpiTemplate::route('/create'),
            'view' => ViewKpiTemplate::route('/{record}'),
            'edit' => EditKpiTemplate::route('/{record}/edit'),
        ];
    }
}
