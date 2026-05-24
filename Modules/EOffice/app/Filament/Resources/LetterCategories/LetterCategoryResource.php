<?php

namespace Modules\EOffice\Filament\Resources\LetterCategories;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\EOffice\Filament\Resources\LetterCategories\Pages\CreateLetterCategory;
use Modules\EOffice\Filament\Resources\LetterCategories\Pages\EditLetterCategory;
use Modules\EOffice\Filament\Resources\LetterCategories\Pages\ListLetterCategories;
use Modules\EOffice\Filament\Resources\LetterCategories\Pages\ViewLetterCategory;
use Modules\EOffice\Filament\Resources\LetterCategories\Schemas\LetterCategoryForm;
use Modules\EOffice\Filament\Resources\LetterCategories\Schemas\LetterCategoryInfolist;
use Modules\EOffice\Filament\Resources\LetterCategories\Tables\LetterCategoriesTable;
use Modules\EOffice\Models\LetterCategory;

class LetterCategoryResource extends ModuleResource
{
    protected static ?string $model = LetterCategory::class;

    public static function form(Schema $schema): Schema
    {
        return LetterCategoryForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return LetterCategoryInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LetterCategoriesTable::configure($table);
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
            'index' => ListLetterCategories::route('/'),
            'create' => CreateLetterCategory::route('/create'),
            'view' => ViewLetterCategory::route('/{record}'),
            'edit' => EditLetterCategory::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
