<?php

namespace Modules\Cms\Filament\Resources\ArticleCategories;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Cms\Filament\Resources\ArticleCategories\Pages\CreateArticleCategory;
use Modules\Cms\Filament\Resources\ArticleCategories\Pages\EditArticleCategory;
use Modules\Cms\Filament\Resources\ArticleCategories\Pages\ListArticleCategories;
use Modules\Cms\Filament\Resources\ArticleCategories\Pages\ViewArticleCategory;
use Modules\Cms\Filament\Resources\ArticleCategories\Schemas\ArticleCategoryForm;
use Modules\Cms\Filament\Resources\ArticleCategories\Schemas\ArticleCategoryInfolist;
use Modules\Cms\Filament\Resources\ArticleCategories\Tables\ArticleCategoriesTable;
use Modules\Cms\Models\ArticleCategory;
use Modules\Core\Filament\Support\ModuleResource;

class ArticleCategoryResource extends ModuleResource
{
    protected static ?string $model = ArticleCategory::class;

    public static function form(Schema $schema): Schema
    {
        return ArticleCategoryForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ArticleCategoryInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ArticleCategoriesTable::configure($table);
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
            'index' => ListArticleCategories::route('/'),
            'create' => CreateArticleCategory::route('/create'),
            'view' => ViewArticleCategory::route('/{record}'),
            'edit' => EditArticleCategory::route('/{record}/edit'),
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
