<?php

namespace Modules\Finance\Filament\Resources\Budgets\Schemas;

use Filament\Facades\Filament;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;

class BudgetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant_id'))
                    ->relationship('tenant', 'name')
                    ->default(Filament::getTenant()?->getKey())
                    ->disabled(Filament::getTenant() !== null)
                    ->dehydrated()
                    ->required(),
                Select::make('organization_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('organization_id'))
                    ->relationship('organization', 'name', modifyQueryUsing: function ($query): void {
                        if (Filament::getTenant()) {
                            $query->where('tenant_id', Filament::getTenant()->getKey());
                        }
                    })
                    ->required(),
                Select::make('chart_of_account_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('chart_of_account_id'))
                    ->relationship('chartOfAccount', 'name', modifyQueryUsing: function ($query): void {
                        if (Filament::getTenant()) {
                            $query->where('tenant_id', Filament::getTenant()->getKey());
                        }
                    })
                    ->required(),
                TextInput::make('approved_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('approved_by'))
                    ->numeric()
                    ->disabled(),
                TextInput::make('fiscal_year')
                    ->label(\Modules\Core\Support\FilamentUi::field('fiscal_year'))
                    ->required(),
                TextInput::make('name')
                    ->label(\Modules\Core\Support\FilamentUi::field('name'))
                    ->required(),
                TextInput::make('code')
                    ->label(\Modules\Core\Support\FilamentUi::field('code'))
                    ->required(),
                TextInput::make('allocated_amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('allocated_amount'))
                    ->required()
                    ->numeric(),
                TextInput::make('used_amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('used_amount'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('remaining_amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('remaining_amount'))
                    ->required()
                    ->numeric(),
                Textarea::make('description')
                    ->label(\Modules\Core\Support\FilamentUi::field('description'))
                    ->columnSpanFull(),
                TextInput::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status'))
                    ->required()
                    ->default('draft'),
                DateTimePicker::make('approved_at')
                    ->disabled(),
            ]);
    }
}
