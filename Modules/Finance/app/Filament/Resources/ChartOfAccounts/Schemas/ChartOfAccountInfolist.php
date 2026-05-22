<?php

namespace Modules\Finance\Filament\Resources\ChartOfAccounts\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class ChartOfAccountInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Account Details'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tenant.name')
                            ->label(FilamentUi::text('Tenant')),
                        TextEntry::make('organization.name')
                            ->label(FilamentUi::text('Organization')),
                        TextEntry::make('parent.name')
                            ->label(FilamentUi::text('Parent'))
                            ->placeholder('-'),
                        TextEntry::make('code')
                            ->label(FilamentUi::field('code')),
                        TextEntry::make('name')
                            ->label(FilamentUi::field('name')),
                        TextEntry::make('level')
                            ->label(FilamentUi::field('level'))
                            ->numeric(),
                        TextEntry::make('type')
                            ->label(FilamentUi::field('type'))
                            ->placeholder('-'),
                        TextEntry::make('category')
                            ->label(FilamentUi::field('category'))
                            ->placeholder('-'),
                        TextEntry::make('normal_balance')
                            ->label(FilamentUi::field('normal_balance'))
                            ->placeholder('-'),
                        TextEntry::make('description')
                            ->label(FilamentUi::field('description'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Bank Account'))
                    ->columns(2)
                    ->schema([
                        IconEntry::make('is_bank_account')
                            ->boolean(),
                        TextEntry::make('bank_name')
                            ->label(FilamentUi::field('bank_name'))
                            ->placeholder('-'),
                        TextEntry::make('bank_account_number')
                            ->label(FilamentUi::field('bank_account_number'))
                            ->placeholder('-'),
                        TextEntry::make('bank_account_holder')
                            ->label(FilamentUi::field('bank_account_holder'))
                            ->placeholder('-'),
                    ]),

                Section::make(FilamentUi::text('Balances & Status'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('opening_balance')
                            ->label(FilamentUi::field('opening_balance'))
                            ->numeric(),
                        TextEntry::make('current_balance')
                            ->label(FilamentUi::field('current_balance'))
                            ->numeric(),
                        IconEntry::make('is_active')
                            ->boolean(),
                        IconEntry::make('is_locked')
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
