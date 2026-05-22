<?php

namespace Modules\Finance\Filament\Resources\TuitionTypes\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class TuitionTypeInfolist
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
                            ->label(FilamentUi::text('Organization')),
                        TextEntry::make('code')
                            ->label(FilamentUi::field('code'))
                            ->placeholder('-'),
                        TextEntry::make('name')
                            ->label(FilamentUi::field('name')),
                        TextEntry::make('education_level')
                            ->label(FilamentUi::field('education_level'))
                            ->placeholder('-'),
                        TextEntry::make('description')
                            ->label(FilamentUi::field('description'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Billing'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('amount')
                            ->label(FilamentUi::field('amount'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('frequency')
                            ->label(FilamentUi::field('frequency'))
                            ->placeholder('-'),
                        TextEntry::make('due_day')
                            ->label(FilamentUi::field('due_day'))
                            ->numeric(),
                        TextEntry::make('grace_period_days')
                            ->label(FilamentUi::field('grace_period_days'))
                            ->numeric(),
                    ]),

                Section::make(FilamentUi::text('Late Fees & Discounts'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('late_fee_percentage')
                            ->label(FilamentUi::field('late_fee_percentage'))
                            ->numeric(),
                        TextEntry::make('late_fee_fixed')
                            ->label(FilamentUi::field('late_fee_fixed'))
                            ->numeric(),
                        IconEntry::make('discount_eligible')
                            ->boolean(),
                        IconEntry::make('is_active')
                            ->boolean(),
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
