<?php

namespace Modules\Core\Filament\Resources\TenantRoles\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class TenantRoleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Info')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tenant.name')
                            ->label(FilamentUi::text('Tenant')),
                        TextEntry::make('name')
                            ->label(FilamentUi::field('name')),
                        TextEntry::make('slug')
                            ->label(FilamentUi::field('slug')),
                        TextEntry::make('level')
                            ->label(FilamentUi::field('level'))
                            ->placeholder('-'),
                        TextEntry::make('description')
                            ->label(FilamentUi::field('description'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make('Permissions')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('permissions')
                            ->label(FilamentUi::field('permissions'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make('Settings')
                    ->columns(2)
                    ->schema([
                        IconEntry::make('is_default')
                            ->boolean(),
                        IconEntry::make('is_super_admin')
                            ->boolean(),
                        TextEntry::make('dashboard_route')
                            ->label(FilamentUi::field('dashboard_route'))
                            ->placeholder('-'),
                    ]),

                Section::make('Timestamps')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label(FilamentUi::field('created_at'))
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->label(FilamentUi::field('updated_at'))
                            ->dateTime()
                            ->placeholder('-'),
                    ]),
            ]);
    }
}
