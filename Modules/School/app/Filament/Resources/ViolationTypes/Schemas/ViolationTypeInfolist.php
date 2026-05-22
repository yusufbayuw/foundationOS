<?php

namespace Modules\School\Filament\Resources\ViolationTypes\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class ViolationTypeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('General Information'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tenant.name')
                            ->label(FilamentUi::text('Tenant')),
                        TextEntry::make('organization.name')
                            ->label(FilamentUi::text('Organization'))
                            ->placeholder('-'),
                        TextEntry::make('code')
                            ->label(FilamentUi::field('code')),
                        TextEntry::make('name')
                            ->label(FilamentUi::field('name')),
                    ]),

                Section::make(FilamentUi::text('Classification'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('category')
                            ->label(FilamentUi::field('category'))
                            ->placeholder('-'),
                        TextEntry::make('severity_level')
                            ->label(FilamentUi::field('severity_level'))
                            ->placeholder('-'),
                        TextEntry::make('point_weight')
                            ->label(FilamentUi::field('point_weight'))
                            ->numeric(),
                        IconEntry::make('is_active')
                            ->boolean(),
                    ]),

                Section::make(FilamentUi::text('Details & Actions'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('default_sanctions')
                            ->label(FilamentUi::field('default_sanctions'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('description')
                            ->label(FilamentUi::field('description'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('prevention_measures')
                            ->label(FilamentUi::field('prevention_measures'))
                            ->placeholder('-')
                            ->columnSpanFull(),
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
