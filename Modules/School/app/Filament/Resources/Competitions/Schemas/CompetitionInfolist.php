<?php

namespace Modules\School\Filament\Resources\Competitions\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class CompetitionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(FilamentUi::text('General information'))
                ->schema([
                    TextEntry::make('tenant_id')
                        ->label(FilamentUi::field('tenant_id'))
                        ->placeholder('-'),
                    TextEntry::make('name')
                        ->label(FilamentUi::field('name'))
                        ->placeholder('-'),
                    TextEntry::make('level')
                        ->label(FilamentUi::field('level'))
                        ->placeholder('-'),
                    TextEntry::make('held_at')
                        ->label(FilamentUi::field('held_at'))
                        ->dateTime()
                        ->placeholder('-'),
                    TextEntry::make('status')
                        ->label(FilamentUi::field('status'))
                        ->placeholder('-'),
                ])
                ->columns(2),
        ]);
    }
}
