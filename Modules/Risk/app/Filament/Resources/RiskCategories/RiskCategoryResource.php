<?php

namespace Modules\Risk\Filament\Resources\RiskCategories;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Risk\Filament\Resources\RiskCategories\Pages\CreateRiskCategory;
use Modules\Risk\Filament\Resources\RiskCategories\Pages\EditRiskCategory;
use Modules\Risk\Filament\Resources\RiskCategories\Pages\ListRiskCategories;
use Modules\Risk\Filament\Resources\RiskCategories\Pages\ViewRiskCategory;
use Modules\Risk\Filament\Resources\RiskCategories\Schemas\RiskCategoryForm;
use Modules\Risk\Filament\Resources\RiskCategories\Schemas\RiskCategoryInfolist;
use Modules\Risk\Filament\Resources\RiskCategories\Tables\RiskCategoriesTable;
use Modules\Risk\Models\RiskCategory;

class RiskCategoryResource extends ModuleResource
{
    protected static ?string $model = RiskCategory::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return RiskCategoryForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return RiskCategoryInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RiskCategoriesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRiskCategories::route('/'),
            'create' => CreateRiskCategory::route('/create'),
            'view' => ViewRiskCategory::route('/{record}'),
            'edit' => EditRiskCategory::route('/{record}/edit'),
        ];
    }
}
