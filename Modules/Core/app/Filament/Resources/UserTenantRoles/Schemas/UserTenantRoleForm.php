<?php

namespace Modules\Core\Filament\Resources\UserTenantRoles\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class UserTenantRoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Info')
                    ->columns(2)
                    ->schema([
                        Select::make('user_id')
                            ->label(FilamentUi::field('user_id'))
                            ->relationship('user', 'name')
                            ->required(),
                        TenantField::make(),
                        Select::make('organization_id')
                            ->label(FilamentUi::field('organization_id'))
                            ->relationship('organization', 'name'),
                        Select::make('tenant_role_id')
                            ->label(FilamentUi::field('tenant_role_id'))
                            ->relationship('tenantRole', 'name')
                            ->required(),
                    ]),

                Section::make('Assignment')
                    ->columns(2)
                    ->schema([
                        TextInput::make('assigned_by')
                            ->label(FilamentUi::field('assigned_by'))
                            ->numeric(),
                        DateTimePicker::make('assigned_at'),
                        DateTimePicker::make('expires_at'),
                    ]),

                Section::make('Settings')
                    ->columns(2)
                    ->schema([
                        Toggle::make('is_primary')
                            ->label(FilamentUi::field('is_primary'))
                            ->required(),
                    ]),
            ]);
    }
}
