<?php

namespace Modules\Core\Filament\Resources\TenantRoles\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;

class TenantRoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TenantField::make(),
                TextInput::make('name')
                    ->label(\Modules\Core\Support\FilamentUi::field('name'))
                    ->required(),
                TextInput::make('slug')
                    ->label(\Modules\Core\Support\FilamentUi::field('slug'))
                    ->required(),
                Textarea::make('description')
                    ->label(\Modules\Core\Support\FilamentUi::field('description'))
                    ->columnSpanFull(),
                TextInput::make('level')
                    ->label(\Modules\Core\Support\FilamentUi::field('level')),
                Textarea::make('permissions')
                    ->label(\Modules\Core\Support\FilamentUi::field('permissions'))
                    ->columnSpanFull(),
                Toggle::make('is_default')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_default'))
                    ->required(),
                Toggle::make('is_super_admin')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_super_admin'))
                    ->required(),
                TextInput::make('dashboard_route')
                    ->label(\Modules\Core\Support\FilamentUi::field('dashboard_route'))
            ]);
    }
}
