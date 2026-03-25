<?php

namespace Modules\Finance\Filament\Resources\ChartOfAccounts\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ChartOfAccountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant_id'))
                    ->relationship('tenant', 'name')
                    ->required(),
                Select::make('organization_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('organization_id'))
                    ->relationship('organization', 'name')
                    ->required(),
                Select::make('parent_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('parent_id'))
                    ->relationship('parent', 'name'),
                TextInput::make('code')
                    ->label(\Modules\Core\Support\FilamentUi::field('code'))
                    ->required(),
                TextInput::make('name')
                    ->label(\Modules\Core\Support\FilamentUi::field('name'))
                    ->required(),
                TextInput::make('level')
                    ->label(\Modules\Core\Support\FilamentUi::field('level'))
                    ->required()
                    ->numeric()
                    ->default(1),
                TextInput::make('type')
                    ->label(\Modules\Core\Support\FilamentUi::field('type')),
                TextInput::make('category')
                    ->label(\Modules\Core\Support\FilamentUi::field('category')),
                TextInput::make('normal_balance')
                    ->label(\Modules\Core\Support\FilamentUi::field('normal_balance')),
                Toggle::make('is_bank_account')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_bank_account'))
                    ->required(),
                TextInput::make('bank_name')
                    ->label(\Modules\Core\Support\FilamentUi::field('bank_name')),
                TextInput::make('bank_account_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('bank_account_number')),
                TextInput::make('bank_account_holder')
                    ->label(\Modules\Core\Support\FilamentUi::field('bank_account_holder')),
                Toggle::make('is_active')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_active'))
                    ->required(),
                Toggle::make('is_locked')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_locked'))
                    ->required(),
                Textarea::make('description')
                    ->label(\Modules\Core\Support\FilamentUi::field('description'))
                    ->columnSpanFull(),
                TextInput::make('opening_balance')
                    ->label(\Modules\Core\Support\FilamentUi::field('opening_balance'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('current_balance')
                    ->label(\Modules\Core\Support\FilamentUi::field('current_balance'))
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
