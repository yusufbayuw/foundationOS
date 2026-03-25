<?php

namespace Modules\Core\Filament\Resources\UserTenantRoles;

use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Resources\UserTenantRoles\Pages\CreateUserTenantRole;
use Modules\Core\Filament\Resources\UserTenantRoles\Pages\EditUserTenantRole;
use Modules\Core\Filament\Resources\UserTenantRoles\Pages\ListUserTenantRoles;
use Modules\Core\Filament\Resources\UserTenantRoles\Pages\ViewUserTenantRole;
use Modules\Core\Filament\Resources\UserTenantRoles\Schemas\UserTenantRoleForm;
use Modules\Core\Filament\Resources\UserTenantRoles\Schemas\UserTenantRoleInfolist;
use Modules\Core\Filament\Resources\UserTenantRoles\Tables\UserTenantRolesTable;
use Modules\Core\Models\UserTenantRole;

class UserTenantRoleResource extends LocalizedResource
{
    protected static ?string $model = UserTenantRole::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return UserTenantRoleForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return UserTenantRoleInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UserTenantRolesTable::configure($table);
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
            'index' => ListUserTenantRoles::route('/'),
            'create' => CreateUserTenantRole::route('/create'),
            'view' => ViewUserTenantRole::route('/{record}'),
            'edit' => EditUserTenantRole::route('/{record}/edit'),
        ];
    }
}
