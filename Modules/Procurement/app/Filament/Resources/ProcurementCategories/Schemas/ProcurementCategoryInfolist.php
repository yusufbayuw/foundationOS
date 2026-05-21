<?php

namespace Modules\Procurement\Filament\Resources\ProcurementCategories\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class ProcurementCategoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Category Information')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tenant.name')
                            ->label(FilamentUi::text('Tenant')),
                        TextEntry::make('organization.name')
                            ->label(FilamentUi::text('Organization'))
                            ->placeholder('-'),
                        TextEntry::make('parent.name')
                            ->label(FilamentUi::text('Parent'))
                            ->placeholder('-'),
                        TextEntry::make('code')
                            ->label(FilamentUi::field('code'))
                            ->placeholder('-'),
                        TextEntry::make('name')
                            ->label(FilamentUi::field('name')),
                        TextEntry::make('type')
                            ->label(FilamentUi::field('type')),
                    ]),

                Section::make('Details')
                    ->columns(1)
                    ->schema([
                        TextEntry::make('description')
                            ->label(FilamentUi::field('description'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make('Status')
                    ->columns(2)
                    ->schema([
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
