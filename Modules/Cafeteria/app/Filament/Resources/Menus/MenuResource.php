<?php

namespace Modules\Cafeteria\Filament\Resources\Menus;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Cafeteria\Filament\Resources\Menus\Pages\CreateMenu;
use Modules\Cafeteria\Filament\Resources\Menus\Pages\EditMenu;
use Modules\Cafeteria\Filament\Resources\Menus\Pages\ListMenus;
use Modules\Cafeteria\Filament\Resources\Menus\Pages\ViewMenu;
use Modules\Cafeteria\Filament\Resources\Menus\Schemas\MenuForm;
use Modules\Cafeteria\Filament\Resources\Menus\Tables\MenusTable;
use Modules\Cafeteria\Models\Menu;
use Modules\Core\Filament\Support\ModuleResource;

class MenuResource extends ModuleResource
{
    protected static ?string $model = Menu::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return MenuForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MenusTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMenus::route('/'),
            'create' => CreateMenu::route('/create'),
            'view' => ViewMenu::route('/{record}'),
            'edit' => EditMenu::route('/{record}/edit'),
        ];
    }
}
