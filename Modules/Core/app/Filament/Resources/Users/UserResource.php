<?php

namespace Modules\Core\Filament\Resources\Users;

use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Resources\Users\Pages\CreateUser;
use Modules\Core\Filament\Resources\Users\Pages\EditUser;
use Modules\Core\Filament\Resources\Users\Pages\ListUsers;
use Modules\Core\Filament\Resources\Users\Pages\ViewUser;
use Modules\Core\Filament\Resources\Users\Schemas\UserForm;
use Modules\Core\Filament\Resources\Users\Schemas\UserInfolist;
use Modules\Core\Filament\Resources\Users\Tables\UsersTable;
use Modules\Core\Models\User;

class UserResource extends LocalizedResource
{
    protected static ?string $model = User::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return UserInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
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
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'view' => ViewUser::route('/{record}'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
