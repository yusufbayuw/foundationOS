<?php

namespace Modules\Finance\Filament\Resources\ChartOfAccounts\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class ChartOfAccountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Account Details')
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('organization_id')
                            ->label(FilamentUi::field('organization_id'))
                            ->relationship('organization', 'name')
                            ->required(),
                        Select::make('parent_id')
                            ->label(FilamentUi::field('parent_id'))
                            ->relationship('parent', 'name'),
                        TextInput::make('code')
                            ->label(FilamentUi::field('code'))
                            ->required(),
                        TextInput::make('name')
                            ->label(FilamentUi::field('name'))
                            ->required(),
                        TextInput::make('level')
                            ->label(FilamentUi::field('level'))
                            ->required()
                            ->numeric()
                            ->default(1),
                        TextInput::make('type')
                            ->label(FilamentUi::field('type')),
                        TextInput::make('category')
                            ->label(FilamentUi::field('category')),
                        TextInput::make('normal_balance')
                            ->label(FilamentUi::field('normal_balance')),
                        Textarea::make('description')
                            ->label(FilamentUi::field('description'))
                            ->columnSpanFull(),
                    ]),

                Section::make('Bank Account')
                    ->columns(2)
                    ->schema([
                        Toggle::make('is_bank_account')
                            ->label(FilamentUi::field('is_bank_account'))
                            ->required(),
                        TextInput::make('bank_name')
                            ->label(FilamentUi::field('bank_name')),
                        TextInput::make('bank_account_number')
                            ->label(FilamentUi::field('bank_account_number')),
                        TextInput::make('bank_account_holder')
                            ->label(FilamentUi::field('bank_account_holder')),
                    ]),

                Section::make('Balances & Status')
                    ->columns(2)
                    ->schema([
                        TextInput::make('opening_balance')
                            ->label(FilamentUi::field('opening_balance'))
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('current_balance')
                            ->label(FilamentUi::field('current_balance'))
                            ->required()
                            ->numeric()
                            ->default(0),
                        Toggle::make('is_active')
                            ->label(FilamentUi::field('is_active'))
                            ->required(),
                        Toggle::make('is_locked')
                            ->label(FilamentUi::field('is_locked'))
                            ->required(),
                    ]),
            ]);
    }
}
