<?php

namespace Modules\Finance\Filament\Resources\StudentInvoices\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\Core\Support\FilamentUi;
use Modules\Finance\Models\ChartOfAccount;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('payment_number')
                ->label(FilamentUi::field('payment_number'))
                ->required(),

            DatePicker::make('payment_date')
                ->label(FilamentUi::field('payment_date'))
                ->required(),

            TextInput::make('amount')
                ->label(FilamentUi::field('amount'))
                ->numeric()
                ->prefix('Rp')
                ->required(),

            TextInput::make('payment_method')
                ->label(FilamentUi::field('payment_method')),

            Select::make('chart_of_account_id')
                ->label(FilamentUi::field('chart_of_account_id'))
                ->options(fn () => ChartOfAccount::withoutTenantScope()
                    ->where('tenant_id', filament()->getTenant()?->id)
                    ->where('is_active', true)
                    ->orderBy('code')
                    ->pluck('name', 'id')
                    ->toArray()
                )
                ->searchable()
                ->nullable(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('payment_number')
                    ->label(FilamentUi::field('payment_number'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('payment_date')
                    ->label(FilamentUi::field('payment_date'))
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label(FilamentUi::field('amount'))
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('payment_method')
                    ->label(FilamentUi::field('payment_method'))
                    ->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->label(FilamentUi::field('status'))
                    ->badge(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['tenant_id'] = filament()->getTenant()?->id;

                        return $data;
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
