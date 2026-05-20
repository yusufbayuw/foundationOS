<?php

namespace Modules\Core\Filament\Resources\TenantSettings\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class TenantSettingInfolist
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
                        TextEntry::make('group')
                            ->label(FilamentUi::field('group'))
                            ->placeholder('-'),
                        TextEntry::make('key')
                            ->label(FilamentUi::field('key')),
                        TextEntry::make('type')
                            ->label(FilamentUi::field('type')),
                    ]),

                Section::make('Value')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('value')
                            ->label(FilamentUi::field('value'))
                            ->placeholder('-')
                            ->columnSpanFull(),
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
