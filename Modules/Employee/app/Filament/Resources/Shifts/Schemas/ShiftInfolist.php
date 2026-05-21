<?php

namespace Modules\Employee\Filament\Resources\Shifts\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class ShiftInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('General Information')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tenant.name')
                            ->label(FilamentUi::text('Tenant')),
                        TextEntry::make('organization.name')
                            ->label(FilamentUi::text('Organization')),
                        TextEntry::make('code')
                            ->label(FilamentUi::field('code')),
                        TextEntry::make('name')
                            ->label(FilamentUi::field('name')),
                        TextEntry::make('color')
                            ->label(FilamentUi::field('color'))
                            ->placeholder('-'),
                    ]),

                Section::make('Shift Schedule')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('start_time')
                            ->label(FilamentUi::field('start_time'))
                            ->time(),
                        TextEntry::make('end_time')
                            ->label(FilamentUi::field('end_time'))
                            ->time(),
                        TextEntry::make('break_duration_minutes')
                            ->label(FilamentUi::field('break_duration_minutes'))
                            ->numeric(),
                    ]),

                Section::make('Settings')
                    ->columns(2)
                    ->schema([
                        IconEntry::make('is_night_shift')
                            ->boolean(),
                        IconEntry::make('is_active')
                            ->boolean(),
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
