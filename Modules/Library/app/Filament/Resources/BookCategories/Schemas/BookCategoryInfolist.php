<?php

namespace Modules\Library\Filament\Resources\BookCategories\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class BookCategoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Scope')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tenant.name')
                            ->label(FilamentUi::text('Tenant')),
                        TextEntry::make('organization.name')
                            ->label(FilamentUi::text('Organization')),
                    ]),

                Section::make('Category Details')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('code')
                            ->label(FilamentUi::field('code')),
                        TextEntry::make('name')
                            ->label(FilamentUi::field('name')),
                        TextEntry::make('description')
                            ->label(FilamentUi::field('description'))
                            ->placeholder('-')
                            ->columnSpanFull(),
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
