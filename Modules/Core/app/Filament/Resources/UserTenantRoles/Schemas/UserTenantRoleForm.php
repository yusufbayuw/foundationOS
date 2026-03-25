<?php

namespace Modules\Core\Filament\Resources\UserTenantRoles\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserTenantRoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('user_id'))
                    ->relationship('user', 'name')
                    ->required(),
                Select::make('tenant_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant_id'))
                    ->relationship('tenant', 'name')
                    ->required(),
                Select::make('organization_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('organization_id'))
                    ->relationship('organization', 'name'),
                Select::make('tenant_role_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant_role_id'))
                    ->relationship('tenantRole', 'name')
                    ->required(),
                TextInput::make('assigned_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('assigned_by'))
                    ->numeric(),
                DateTimePicker::make('assigned_at'),
                DateTimePicker::make('expires_at'),
                Toggle::make('is_primary')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_primary'))
                    ->required(),
            ]);
    }
}
