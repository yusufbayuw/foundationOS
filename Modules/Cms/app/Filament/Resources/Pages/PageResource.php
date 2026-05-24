<?php

namespace Modules\Cms\Filament\Resources\Pages;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Cms\Filament\Resources\Pages\Pages\CreatePage;
use Modules\Cms\Filament\Resources\Pages\Pages\EditPage;
use Modules\Cms\Filament\Resources\Pages\Pages\ListPages;
use Modules\Cms\Filament\Resources\Pages\Pages\ViewPage;
use Modules\Cms\Filament\Resources\Pages\Schemas\PageForm;
use Modules\Cms\Filament\Resources\Pages\Schemas\PageInfolist;
use Modules\Cms\Filament\Resources\Pages\Tables\PagesTable;
use Modules\Cms\Models\Page;
use Modules\Core\Filament\Support\ModuleResource;

class PageResource extends ModuleResource
{
    protected static ?string $model = Page::class;

    public static function form(Schema $schema): Schema
    {
        return PageForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PageInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PagesTable::configure($table);
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
            'index' => ListPages::route('/'),
            'create' => CreatePage::route('/create'),
            'view' => ViewPage::route('/{record}'),
            'edit' => EditPage::route('/{record}/edit'),
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
