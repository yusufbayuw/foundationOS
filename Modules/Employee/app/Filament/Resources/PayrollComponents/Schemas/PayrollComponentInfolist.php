<?php

namespace Modules\Employee\Filament\Resources\PayrollComponents\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class PayrollComponentInfolist
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
                        TextEntry::make('type')
                            ->label(FilamentUi::field('type'))
                            ->placeholder('-'),
                        TextEntry::make('category')
                            ->label(FilamentUi::field('category'))
                            ->placeholder('-'),
                    ]),

                Section::make('Calculation')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('calculation_type')
                            ->label(FilamentUi::field('calculation_type'))
                            ->placeholder('-'),
                        TextEntry::make('amount')
                            ->label(FilamentUi::field('amount'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('percentage')
                            ->label(FilamentUi::field('percentage'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('formula')
                            ->label(FilamentUi::field('formula'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make('Settings')
                    ->columns(2)
                    ->schema([
                        IconEntry::make('is_taxable')
                            ->boolean(),
                        IconEntry::make('is_mandatory')
                            ->boolean(),
                        IconEntry::make('is_active')
                            ->boolean(),
                        TextEntry::make('display_order')
                            ->label(FilamentUi::field('display_order'))
                            ->numeric(),
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
