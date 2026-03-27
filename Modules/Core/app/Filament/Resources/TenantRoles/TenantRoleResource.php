<?php

namespace Modules\Core\Filament\Resources\TenantRoles;

use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Resources\TenantRoles\Pages\CreateTenantRole;
use Modules\Core\Filament\Resources\TenantRoles\Pages\EditTenantRole;
use Modules\Core\Filament\Resources\TenantRoles\Pages\ListTenantRoles;
use Modules\Core\Filament\Resources\TenantRoles\Pages\ViewTenantRole;
use Modules\Core\Filament\Resources\TenantRoles\RelationManagers\UserTenantRolesRelationManager;
use Modules\Core\Filament\Resources\TenantRoles\Schemas\TenantRoleForm;
use Modules\Core\Filament\Resources\TenantRoles\Schemas\TenantRoleInfolist;
use Modules\Core\Filament\Resources\TenantRoles\Tables\TenantRolesTable;
use Modules\Core\Models\TenantRole;

class TenantRoleResource extends LocalizedResource
{
    protected static ?string $model = TenantRole::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return TenantRoleForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TenantRoleInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TenantRolesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            UserTenantRolesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTenantRoles::route('/'),
            'create' => CreateTenantRole::route('/create'),
            'view' => ViewTenantRole::route('/{record}'),
            'edit' => EditTenantRole::route('/{record}/edit'),
        ];
    }
}
