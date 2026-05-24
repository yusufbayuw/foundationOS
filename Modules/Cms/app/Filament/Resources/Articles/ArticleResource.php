<?php

namespace Modules\Cms\Filament\Resources\Articles;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Cms\Filament\Resources\Articles\Pages\CreateArticle;
use Modules\Cms\Filament\Resources\Articles\Pages\EditArticle;
use Modules\Cms\Filament\Resources\Articles\Pages\ListArticles;
use Modules\Cms\Filament\Resources\Articles\Pages\ViewArticle;
use Modules\Cms\Filament\Resources\Articles\Schemas\ArticleForm;
use Modules\Cms\Filament\Resources\Articles\Schemas\ArticleInfolist;
use Modules\Cms\Filament\Resources\Articles\Tables\ArticlesTable;
use Modules\Cms\Models\Article;
use Modules\Core\Filament\Support\ModuleResource;

class ArticleResource extends ModuleResource
{
    protected static ?string $model = Article::class;

    public static function form(Schema $schema): Schema
    {
        return ArticleForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ArticleInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ArticlesTable::configure($table);
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
            'index' => ListArticles::route('/'),
            'create' => CreateArticle::route('/create'),
            'view' => ViewArticle::route('/{record}'),
            'edit' => EditArticle::route('/{record}/edit'),
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
