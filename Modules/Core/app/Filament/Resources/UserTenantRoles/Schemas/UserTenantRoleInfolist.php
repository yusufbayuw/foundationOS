<?php

namespace Modules\Core\Filament\Resources\UserTenantRoles\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class UserTenantRoleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Basic Info'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('user.name')
                            ->label(FilamentUi::text('User')),
                        TextEntry::make('tenant.name')
                            ->label(FilamentUi::text('Tenant')),
                        TextEntry::make('organization.name')
                            ->label(FilamentUi::text('Organization'))
                            ->placeholder('-'),
                        TextEntry::make('tenantRole.name')
                            ->label(FilamentUi::text('Tenant role')),
                    ]),

                Section::make(FilamentUi::text('Assignment'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('assigned_by')
                            ->label(FilamentUi::field('assigned_by'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('assigned_at')
                            ->label(FilamentUi::field('assigned_at'))
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('expires_at')
                            ->label(FilamentUi::field('expires_at'))
                            ->dateTime()
                            ->placeholder('-'),
                    ]),

                Section::make(FilamentUi::text('Settings'))
                    ->columns(2)
                    ->schema([
                        IconEntry::make('is_primary')
                            ->boolean(),
                    ]),

                Section::make(FilamentUi::text('Timestamps'))
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
