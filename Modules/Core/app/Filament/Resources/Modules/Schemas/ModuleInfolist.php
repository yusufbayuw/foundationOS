<?php

namespace Modules\Core\Filament\Resources\Modules\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class ModuleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Basic Info'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('code')
                            ->label(FilamentUi::field('code')),
                        TextEntry::make('slug')
                            ->label(FilamentUi::field('slug')),
                        TextEntry::make('name')
                            ->label(FilamentUi::field('name')),
                        TextEntry::make('description')
                            ->label(FilamentUi::field('description'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Appearance'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('icon')
                            ->label(FilamentUi::field('icon'))
                            ->placeholder('-'),
                        TextEntry::make('color')
                            ->label(FilamentUi::field('color'))
                            ->placeholder('-'),
                        TextEntry::make('version')
                            ->label(FilamentUi::field('version'))
                            ->placeholder('-'),
                    ]),

                Section::make(FilamentUi::text('Status'))
                    ->columns(2)
                    ->schema([
                        IconEntry::make('is_core')
                            ->boolean(),
                        IconEntry::make('is_active')
                            ->boolean(),
                        IconEntry::make('is_premium')
                            ->boolean(),
                    ]),

                Section::make(FilamentUi::text('Pricing'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('price_monthly')
                            ->label(FilamentUi::field('price_monthly'))
                            ->numeric(),
                        TextEntry::make('price_yearly')
                            ->label(FilamentUi::field('price_yearly'))
                            ->numeric(),
                    ]),

                Section::make(FilamentUi::text('Settings'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('settings_schema')
                            ->label(FilamentUi::field('settings_schema'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('required_modules')
                            ->label(FilamentUi::field('required_modules'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('sort_order')
                            ->label(FilamentUi::field('sort_order'))
                            ->numeric(),
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
