<?php

namespace Modules\Finance\Filament\Resources\Budgets\Schemas;

use App\Support\TypedValue;
use Filament\Facades\Filament;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class BudgetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Budget Details'))
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('organization_id')
                            ->label(FilamentUi::field('organization_id'))
                            ->relationship('organization', 'name', modifyQueryUsing: function (Builder $query): void {
                                if (Filament::getTenant()) {
                                    $query->where($query->getModel()->qualifyColumn('tenant_id'), TypedValue::tenantKey(Filament::getTenant()->getKey()));
                                }
                            })
                            ->required(),
                        Select::make('chart_of_account_id')
                            ->label(FilamentUi::field('chart_of_account_id'))
                            ->relationship('chartOfAccount', 'name', modifyQueryUsing: function (Builder $query): void {
                                if (Filament::getTenant()) {
                                    $query->where($query->getModel()->qualifyColumn('tenant_id'), TypedValue::tenantKey(Filament::getTenant()->getKey()));
                                }
                            })
                            ->required(),
                        TextInput::make('fiscal_year')
                            ->label(FilamentUi::field('fiscal_year'))
                            ->required(),
                        TextInput::make('name')
                            ->label(FilamentUi::field('name'))
                            ->required(),
                        TextInput::make('code')
                            ->label(FilamentUi::field('code'))
                            ->required(),
                        TextInput::make('status')
                            ->label(FilamentUi::field('status'))
                            ->required()
                            ->default('draft'),
                        Textarea::make('description')
                            ->label(FilamentUi::field('description'))
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Amounts'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('allocated_amount')
                            ->label(FilamentUi::field('allocated_amount'))
                            ->required()
                            ->numeric(),
                        TextInput::make('used_amount')
                            ->label(FilamentUi::field('used_amount'))
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('remaining_amount')
                            ->label(FilamentUi::field('remaining_amount'))
                            ->required()
                            ->numeric(),
                    ]),

                Section::make(FilamentUi::text('Approval'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('approved_by')
                            ->label(FilamentUi::field('approved_by'))
                            ->numeric()
                            ->disabled(),
                        DateTimePicker::make('approved_at')
                            ->disabled(),
                    ]),
            ]);
    }
}
