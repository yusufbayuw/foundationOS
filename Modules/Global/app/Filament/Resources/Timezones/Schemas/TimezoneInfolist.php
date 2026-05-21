<?php

namespace Modules\Global\Filament\Resources\Timezones\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TimezoneInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Timezone Details')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('code')
                            ->label(\Modules\Core\Support\FilamentUi::field('code')),
                        TextEntry::make('name')
                            ->label(\Modules\Core\Support\FilamentUi::field('name')),
                        TextEntry::make('utc_offset')
                            ->label(\Modules\Core\Support\FilamentUi::field('utc_offset'))
                            ->placeholder('-'),
                    ]),

                Section::make('Timestamps')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label(\Modules\Core\Support\FilamentUi::field('created_at'))
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->label(\Modules\Core\Support\FilamentUi::field('updated_at'))
                            ->dateTime()
                            ->placeholder('-'),
                    ]),
            ]);
    }
}
