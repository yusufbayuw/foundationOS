<?php

namespace Modules\Procurement\Filament\Resources\ProcurementCategories;

use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Procurement\Filament\Resources\ProcurementCategories\Pages\CreateProcurementCategory;
use Modules\Procurement\Filament\Resources\ProcurementCategories\Pages\EditProcurementCategory;
use Modules\Procurement\Filament\Resources\ProcurementCategories\Pages\ListProcurementCategories;
use Modules\Procurement\Filament\Resources\ProcurementCategories\Pages\ViewProcurementCategory;
use Modules\Procurement\Filament\Resources\ProcurementCategories\Schemas\ProcurementCategoryForm;
use Modules\Procurement\Filament\Resources\ProcurementCategories\Schemas\ProcurementCategoryInfolist;
use Modules\Procurement\Filament\Resources\ProcurementCategories\Tables\ProcurementCategoriesTable;
use Modules\Procurement\Models\ProcurementCategory;

class ProcurementCategoryResource extends LocalizedResource
{
    protected static ?string $model = ProcurementCategory::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ProcurementCategoryForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProcurementCategoryInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProcurementCategoriesTable::configure($table);
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
            'index' => ListProcurementCategories::route('/'),
            'create' => CreateProcurementCategory::route('/create'),
            'view' => ViewProcurementCategory::route('/{record}'),
            'edit' => EditProcurementCategory::route('/{record}/edit'),
        ];
    }
}
