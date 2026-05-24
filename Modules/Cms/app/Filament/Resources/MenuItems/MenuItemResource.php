<?php

namespace Modules\Cms\Filament\Resources\MenuItems;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Cms\Filament\Resources\MenuItems\Pages\CreateMenuItem;
use Modules\Cms\Filament\Resources\MenuItems\Pages\EditMenuItem;
use Modules\Cms\Filament\Resources\MenuItems\Pages\ListMenuItems;
use Modules\Cms\Filament\Resources\MenuItems\Pages\ViewMenuItem;
use Modules\Cms\Filament\Resources\MenuItems\Schemas\MenuItemForm;
use Modules\Cms\Filament\Resources\MenuItems\Schemas\MenuItemInfolist;
use Modules\Cms\Filament\Resources\MenuItems\Tables\MenuItemsTable;
use Modules\Cms\Models\MenuItem;
use Modules\Core\Filament\Support\ModuleResource;

class MenuItemResource extends ModuleResource
{
    protected static ?string $model = MenuItem::class;

    public static function form(Schema $schema): Schema
    {
        return MenuItemForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MenuItemInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MenuItemsTable::configure($table);
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
            'index' => ListMenuItems::route('/'),
            'create' => CreateMenuItem::route('/create'),
            'view' => ViewMenuItem::route('/{record}'),
            'edit' => EditMenuItem::route('/{record}/edit'),
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
