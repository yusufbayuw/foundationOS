<?php

namespace Modules\Global\Filament\Resources\Villages\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VillageInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Village Details')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('district.name')
                            ->label(\Modules\Core\Support\FilamentUi::text('District')),
                        TextEntry::make('code')
                            ->label(\Modules\Core\Support\FilamentUi::field('code')),
                        TextEntry::make('name')
                            ->label(\Modules\Core\Support\FilamentUi::field('name')),
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
