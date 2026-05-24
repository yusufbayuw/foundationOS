<?php

namespace Modules\Helpdesk\Filament\Resources\KnowledgeBaseArticles;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Helpdesk\Filament\Resources\KnowledgeBaseArticles\Pages\CreateKnowledgeBaseArticle;
use Modules\Helpdesk\Filament\Resources\KnowledgeBaseArticles\Pages\EditKnowledgeBaseArticle;
use Modules\Helpdesk\Filament\Resources\KnowledgeBaseArticles\Pages\ListKnowledgeBaseArticles;
use Modules\Helpdesk\Filament\Resources\KnowledgeBaseArticles\Pages\ViewKnowledgeBaseArticle;
use Modules\Helpdesk\Filament\Resources\KnowledgeBaseArticles\Schemas\KnowledgeBaseArticleForm;
use Modules\Helpdesk\Filament\Resources\KnowledgeBaseArticles\Schemas\KnowledgeBaseArticleInfolist;
use Modules\Helpdesk\Filament\Resources\KnowledgeBaseArticles\Tables\KnowledgeBaseArticlesTable;
use Modules\Helpdesk\Models\KnowledgeBaseArticle;

class KnowledgeBaseArticleResource extends ModuleResource
{
    protected static ?string $model = KnowledgeBaseArticle::class;

    public static function form(Schema $schema): Schema
    {
        return KnowledgeBaseArticleForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return KnowledgeBaseArticleInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KnowledgeBaseArticlesTable::configure($table);
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
            'index' => ListKnowledgeBaseArticles::route('/'),
            'create' => CreateKnowledgeBaseArticle::route('/create'),
            'view' => ViewKnowledgeBaseArticle::route('/{record}'),
            'edit' => EditKnowledgeBaseArticle::route('/{record}/edit'),
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
